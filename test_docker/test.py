import requests
import json
import time

chemin = "D:/Téléchargements/"

def save_file(fichier,data):
  data = json.dumps(data)
  with open(fichier, "w" , encoding='utf-8') as fs: 
    fs.write( data )
  fs.close()
  
payload={}
headers = {
  'Accept': 'application/json'
}
headers = {}
saved_token = "cbd3c2c7-fe60-4d6f-8366-7fd438d54e57"
url = "http://192.168.5.11:8080/api/v1/"
url_complet = url + "connect?token=" + saved_token
connect = requests.request("POST", url_complet, headers=headers, data=payload)
connect = json.loads(connect.text)
url_complet = url + "enclosure?token=" + connect["token"]
enclosure = requests.request("GET", url_complet, headers=headers, data=payload)
url_complet = url + "status?token=" + connect["token"]
for j in range(100): # nbre retry
  status = requests.request("GET", url_complet, headers=headers, data=payload)
  save_file(chemin + str(j) + ".json",json.loads(status.text))
  print(status)
  time.sleep(3)
  if status.status_code == 200:
    break