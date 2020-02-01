from freud import Freud
import mysql.connector
from words import Words
from settings import Settings

# Analyze all new dream content
f = Freud()

cnx = mysql.connector.connect(user='root', password=Settings.get('db', 'password'), database='freud')
# Get and process dreams
cursor = cnx.cursor(buffered=True)

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

if cursor.rowcount > 0:
    f = Freud()
    words = Words()
    for (id, text) in cursor:
        text = f.preprocess_dream_text(text)
        dream_word_freq = f.process_dream(id, text)
        for dwf in dream_word_freq:
            words.add_dream_frequency(dwf[0], dwf[1], dwf[2], dwf[3])
else:
    print('No dreams to process.')