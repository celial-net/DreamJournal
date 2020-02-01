import mysql.connector
from freud import Freud
from settings import Settings

# Jung searches for dreams and gives you answers
class Jung:
    def search(self, search_text, user_id=None, limit=None, page=None):
        search_terms = []

        limit = int(limit or 0)
        page = int(page or 0)

        if search_text:
            f = Freud()
            search_text = f.preprocess_dream_text(search_text)
            tokens = f.process_sentence(search_text)
            for token in tokens:
                search_terms.append(token[1])

        if len(search_terms) == 0:
            search_terms.append('')

        if user_id is not None:
            user_condition = "dream.user_id = " + user_id
        else:
            user_condition = "1"

        # Build unions for query to get all dreams containing a search term
        sql = """
    (
        SELECT
           dream_id, dwf.word_id, dwf.frequency
        FROM
            freud.dream_word_freq dwf
        INNER JOIN
            freud.word ON(
                word.id = dwf.word_id
            )
        WHERE
            word.search LIKE %s
    )"""
        first_term = search_terms.pop()
        params = [first_term + "%"]

        for search_term in search_terms:
            sql += "\n\n\tunion\n" + sql
            params.append(search_term + "%")

        # Add in the main query to work on unioned data
        sql = "(" + sql + "\n)"
        sql = """
SELECT
    bin_to_uuid(z.dream_id) AS dream_id,
    SUM(z.frequency) AS total_frequency
FROM
""" + sql + """ z
INNER JOIN
    dj.dream ON dream.id = z.dream_id
WHERE
    """ + user_condition + """
GROUP BY
    dream_id
ORDER BY
    total_frequency DESC, dream_id DESC
        """

        limit_sql = sql
        if limit > 0:
            limit_sql = limit_sql + """
LIMIT
    """ + str(page * limit) + ", " + str(limit)

        cnx = mysql.connector.connect(user='root', password=Settings.get('db', 'password'), database='freud')

        # Get the paged dream results
        cursor = cnx.cursor()
        cursor.execute(limit_sql, params)
        results = {
            'results': [],
            'total': 0
        }
        for result in cursor:
            results['results'].append({
                'id': result[0],
                'frequency': result[1]
            })
        cursor.close()

        # Get the total count of results (horribly inefficient, refactor to use counting)
        cursor = cnx.cursor()
        cursor.execute(sql, params)
        cursor.fetchall()
        results['total'] = cursor.rowcount;
        cursor.close()

        return results
