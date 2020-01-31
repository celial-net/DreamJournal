import mysql.connector


# Class is used for finding, adding, and processing words
class Words:
    cnx = None

    def __init__(self):
        self.cnx = mysql.connector.connect(user='root', password='password', database='freud')

    # Processes a word and returns a (lemmatized, stemmatized) word pair or None on error
    def process(self, word):
        return word, word

    # Takes a normal word, processes it, and adds it returning its id.
    def add_word(self, word):
        (lemmatized_word, stemmatized_word) = self.process(word)

        word_id = self.get_lemmatized_word(lemmatized_word)
        if word_id is not None:
            return word_id
        else:
            return self.add_lemmatized_word(lemmatized_word, stemmatized_word)

    # Inserts a lemmatized and stemmatized word pair
    def add_lemmatized_word(self, lemmatized_word, stemmatized_word):
        if lemmatized_word is None:
            return None
        else:
            word_insert = """INSERT INTO word(word, search) VALUES( %s, %s )"""
            insert_cursor = self.cnx.cursor()
            insert_cursor.execute(word_insert, (lemmatized_word, stemmatized_word))
            word_id = insert_cursor.lastrowid
            insert_cursor.close()
            return word_id

    # Returns the id of a word or None if none is found
    def get_word(self, word):
        (lemmatized_word, stemmatized_word) = self.process(word)
        return self.get_lemmatized_word(lemmatized_word)

    def get_lemmatized_word(self, lemmatized_word):
        if lemmatized_word is None:
            return None
        else:
            word_cursor = self.cnx.cursor()
            word_query = "select id from word where word = %s"
            word_cursor.execute(word_query, (lemmatized_word,))
            word_result = word_cursor.fetchone()
            word_cursor.close()
            # Word exists, use id
            if word_result:
                return word_result[0]
            else:
                return None

    def add_dream_frequency(self, dream_id, lemmatized_word, stemmatized_word, frequency):
        # Find or add word
        # print("adf: " + dream_id + ", " + lemmatized_word + ", " + stemmatized_word + ", " + str(frequency))
        word_id = self.get_lemmatized_word(lemmatized_word)
        if word_id is None:
            word_id = self.add_lemmatized_word(lemmatized_word, stemmatized_word)

        if word_id:
            word_freq_insert = ("""
                       INSERT INTO dream_word_freq(dream_id, word_id, frequency) VALUES(uuid_to_bin( %s ), %s, %s)
                   """)
            freq_insert_cursor = self.cnx.cursor()
            freq_insert_cursor.execute(word_freq_insert, (dream_id, word_id, frequency))
            freq_insert_cursor.close()
            self.cnx.commit()
        else:
            raise Exception('Did not find word id for ' + lemmatized_word + '.')
