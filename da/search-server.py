from jung import Jung
from freud import Freud
from words import Words
import asyncio
import json


async def handle_request(reader, writer):
    data = await reader.read(512)

    if not data:
        response = {
            'code': 400,
            'error': 'no data sent',
            'data': None
        }
    else:
        print("request: " + data.decode())
        request = json.loads(data.decode())
        api = request['api']
        if api == 'search':
            j = Jung()
            response = {
                'code': 200,
                'error': None,
                'data': j.search(request['search_text'], request['user_id'], request['limit'], request['page'])
            }
        elif api == 'add_word':
            sentence = request['word']
            f = Freud()
            sentence = f.preprocess_dream_text(sentence)
            tokens = f.process_sentence(sentence)
            if len(tokens) > 0:
                token = tokens[0]
                words = Words()
                if words.get_lemmatized_word(token[0]) is None:
               		words.add_lemmatized_word(token[0], token[1])
                response = {
                    'code': 200,
                    'error': None,
                    'data': token[0]
                }
            else:
                response = {
                    'code': 400,
                    'error': 'failed to add word',
                    'data': None
                }

    json_response = json.dumps(response)
    print('sending: ' + json_response)
    writer.write(json_response.encode())
    await writer.drain()
    writer.close()

async def main():
    server = await asyncio.start_server(
        handle_request, '127.0.0.1', 1995)

    addr = server.sockets[0].getsockname()
    print(f'Serving on {addr}')

    async with server:
        await server.serve_forever()

asyncio.run(main())
