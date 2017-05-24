import socket
from pymongo import MongoClient
import ast
import ssl
import multiprocessing

def process_request(sock):
    try:
        buf = sock.recv(1024*1024).decode()
        buf1 = sock.recv(1024 * 1024).decode()
        buf2 = sock.recv(1024 * 1024).decode()
        buf3 = sock.recv(1024 * 1024).decode()
        buf4 = sock.recv(1024 * 1024).decode()
        buf = buf[:-1]
        if len(buf1) > 0 and len(buf2) > 0 and len(buf3) > 0 and len(buf4) > 0:
            buf += ", 'pkg': " + buf1 + buf2 + buf3 + buf4 + "}"
        else:
            buf += "}"

        if len(buf) > 0:
            buf = ast.literal_eval(buf)
            if type(buf) is dict:
                client = MongoClient()
                db = client.trump
                exists = db.units.find_one({'hostname': buf['hostname']})

                if exists == None:
                    db.units.insert_one({'hostname': buf['hostname']})
                db.monData.insert_one(buf)
                commands = db.commands.find({'hostname': buf['hostname'], 'status': 'pending'})

                if commands.count() > 0:
                    scheudle = "["
                    for i in commands:
                        scheudle += "'" + i['command'] + "',"
                    scheudle = scheudle[:-1]
                    scheudle += "]"
                    db.commands.update_many(
                        {'hostname': buf['hostname'], 'status': 'pending'},
                        {'$set': {'status': 'Done'}}
                    )
                    conn.send(scheudle.encode())
                client.close()
    finally:
        sock.shutdown(socket.SHUT_RDWR)
        sock.close()

serversocket = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
serversocket.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
serversocket.bind(('', 8089))
serversocket.listen(5) # become a server socket, maximum 5 connections

while True:
    connection, address = serversocket.accept()
    conn = ssl.wrap_socket(connection, server_side=True,
                           certfile="/home/tosti/server.crt",
                           keyfile="/home/tosti/server.key",
                           ssl_version=ssl.PROTOCOL_TLSv1)
    subprocess = multiprocessing.Process(target=process_request, args=(conn,))
    subprocess.start()
serversocket.close()