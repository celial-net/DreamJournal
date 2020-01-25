import nltk
from nltk.tokenize import sent_tokenize
from nltk.tokenize import word_tokenize
from nltk.probability import FreqDist
from nltk.corpus import stopwords
from nltk.stem import PorterStemmer
from nltk.stem.wordnet import WordNetLemmatizer
from nltk.corpus import wordnet
import mysql.connector
from words import Words


# Freud takes the dreams and analyzes them
class Freud:
    cnx = None
    words = None
    stop_words = []
    lem = None
    stem = None

    def __init__(self):
        self.words = Words()
        self.lem = WordNetLemmatizer()
        self.stem = PorterStemmer()
        self.stop_words = stopwords.words("english")
        self.stop_words.extend([",", ".", "!", "?", ";", ":", "n't", "’", "'", "\"", "”", "“"])

    def process(self):
        self.cnx = mysql.connector.connect(user='root', password='password', database='freud')
        # Get and process dreams
        cursor = self.cnx.cursor(buffered=True)
        dream_query = ("""
            select 
                bin_to_uuid(id) as 'id',
                concat(title, '. ', description) as 'text'
            from
                dj.dream
            where
                not exists(
                    select
                        1
                    from
                        freud.dream_word_freq dwf
                    where
                        dwf.dream_id = dream.id
                    limit
                        1
                )
            ;
        """)

        cursor.execute(dream_query)
        for (id, text) in cursor:
            text = self.__preprocess_dream_text(text)
            self.__process_dream(id, text)

        cursor.close()
        self.cnx.commit()
        self.cnx.close()

    # Converts treebank to wordnet format for parts of speech
    def __get_wordnet_pos(self, treebank_tag):
        if treebank_tag.startswith('J'):
            return wordnet.ADJ
        elif treebank_tag.startswith('V'):
            return wordnet.VERB
        elif treebank_tag.startswith('N'):
            return wordnet.NOUN
        elif treebank_tag.startswith('R'):
            return wordnet.ADV
        else:
            # Default to noun
            return wordnet.NOUN

    # Processes a sentence into lemmatized tokens
    def process_sentence(self, s):
        # Break into words
        words = word_tokenize(s)

        # Get parts of speech
        pos = nltk.pos_tag(words)

        # All tokens (lem, stem)
        tokens = []

        for p in pos:
            # Words at this point should all be lowercase
            word = p[0].lower()
            pos = self.__get_wordnet_pos(p[1])

            # Strip out prepositions, stop words, and anything starting with a single qoute
            # (seocnd half of contractions like coulodn't or would've)
            if not p[1].startswith('PRP') and word not in self.stop_words and not word[0] == "'":
                # Lemmatize
                lemmatized_word = self.lem.lemmatize(word, pos)
                lemmatized_word_verb = self.lem.lemmatize(word, 'v')

                # If the verb is shorter, we want that to fix issues with "swimming" being seen
                # as a noun and not lemmatized as "swim"
                if len(lemmatized_word_verb) < len(lemmatized_word):
                    lemmatized_word = lemmatized_word_verb

                # Stemmatize
                stemmatized_word = self.stem.stem(lemmatized_word)

                # Add to tokens
                tokens.append((lemmatized_word, stemmatized_word))

        return tokens

    def __preprocess_dream_text(self, text):
        # Convert weird unicode things to spaces
        text = text.replace('—', ' ')
        return text

    def __process_dream(self, dream_id, text):
        print("Processing dream " + dream_id)
        dream_tokens = []
        # Split into sentences
        sentences = sent_tokenize(text)
        for s in sentences:
            dream_tokens.extend(self.process_sentence(s))

        # Get sorted frequency of words
        for item in FreqDist(dream_tokens).items():
            self.words.add_dream_frequency(dream_id, item[0][0], item[0][1], item[1] / len(dream_tokens))
        print("Finished.\n")