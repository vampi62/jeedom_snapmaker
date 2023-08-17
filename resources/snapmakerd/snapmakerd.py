# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
# 
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.

import shared
import logging
import string
import sys
import os
import time
import datetime
import traceback
import re
import signal
from optparse import OptionParser
from os.path import join
import json
import argparse
import time
import requests

try:
	from jeedom.jeedom import *
except ImportError:
	print("Error: importing module jeedom.jeedom")
	sys.exit(1)

def read_socket(name):
	global JEEDOM_SOCKET_MESSAGE
	while 1:
		if not JEEDOM_SOCKET_MESSAGE.empty():
			logging.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
			message = json.loads(JEEDOM_SOCKET_MESSAGE.get())
			if message['apikey'] != shared.apikey:
				logging.error("Invalid apikey from socket : " + str(message))
				return
			try:
				logging.debug("Message received in socket : " + str(message['cmd']))
				logging.debug("Message received in socket : " + str(message['value']))
			except Exception as e:
				logging.error('Send command to demon error : '+str(e))
		time.sleep(shared.cycle)

payload={}
headers = {
  'Accept': 'application/json'
}
saved_token = "cbd3c2c7-fe60-4d6f-8366-7fd438d54e57"

def printer_connexion(name):
	while 1:
		time.sleep(1)
		if shared.connect_to_printer:
			# ping printer device IP
			response = os.system("ping -c 1 " + shared.printer)
			if response == 0:
				logging.debug("Printer is connected")
				jeedom_socket.send({'apikey':shared.apikey,'cmd':'printer_connected','value':'1'})
				printerconnecthttp = requests.request("POST",'http://'+shared.printer+':8080/api/v1/connect?token=' + shared.token, headers=headers, data=payload)
				logging.debug("Printer connect status code: " + str(printerconnecthttp.status_code))
				printerconnectjson = json.loads(printerconnecthttp.text)
				if shared.token == "":
					shared.token = printerconnectjson['token']
				printerconnectjson["apikey"] = shared.apikey
				printerconnectjson['device'] = shared.device
				jeedom_socket.send_change_immediate(printerconnectjson)
				logging.debug("Token : " + shared.token)
				logging.debug("Printer connect : " + str(printerconnectjson))
				while shared.connect_to_printer:
					time.sleep(1)
					printerstatushttp = requests.request("GET",'http://'+shared.printer+':8080/api/v1/status?token=' + shared.token, headers=headers, data=payload)
					logging.debug("Printer connect status code: " + str(printerstatushttp.status_code))
					printerstatusjson = json.loads(printerstatushttp.text)
					time.sleep(2.5)
					if printerstatusjson['module']["enclosure"]:
						printerenclosurehttp = requests.request("GET",'http://'+shared.printer+':8080/api/v1/enclosure?token=' + shared.token, headers=headers, data=payload)
						logging.debug("Printer connect status code: " + str(printerenclosurehttp.status_code))
						printerenclosurejson = json.loads(printerenclosurehttp.text)
						printerstatusjson["enclosure"] = printerenclosurejson
					time.sleep(1.5)
					printerstatusjson["apikey"] = shared.apikey
					printerstatusjson['device'] = shared.device
					shared.JEEDOM_COM.send_change_immediate(printerstatusjson)
					#if printerstatushttp.status_code != 200:
					#	break
			else:
				logging.debug("Printer is not connected")
				shared.connect_to_printer = False
				jeedom_socket.send({'apikey':shared.apikey,'cmd':'printer_disconnected','value':'1'})




def listen():
	jeedom_socket.open()
	logging.info("Start listening...")
	threading.Thread(target=read_socket, args=('socket',)).start()
	logging.debug('Read Socket Thread Launched')
	threading.Thread(target=printer_connexion, args=('socket',)).start()
	logging.debug('Printer Connexion Thread Launched')


# ----------------------------------------------------------------------------

def handler(signum=None, frame=None):
	logging.debug("Signal %i caught, exiting..." % int(signum))
	shutdown()

def shutdown():
	logging.debug("Shutdown")
	logging.debug("Removing PID file " + str(shared.pidfile))
	try:
		os.remove(shared.pidfile)
	except:
		pass
	try:
		jeedom_socket.close()
	except:
		pass
	try:
		jeedom_serial.close()
	except:
		pass
	logging.debug("Exit 0")
	sys.stdout.flush()
	os._exit(0)

# ----------------------------------------------------------------------------

parser = argparse.ArgumentParser(
    description='Desmond Daemon for Jeedom plugin')
parser.add_argument("--device", help="jeedomID", type=str)
parser.add_argument("--printer", help="IPprinter", type=str)
parser.add_argument("--token", help="Token", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--callback", help="Callback", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--cycle", help="Cycle to send event", type=str)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--socketport", help="Port for Zigbee server", type=str)
args = parser.parse_args()

if args.device:
	shared.device = args.device
if args.printer:
	shared.printer = args.printer
if args.token:
	shared.token = args.token
if args.loglevel:
    shared.log_level = args.loglevel
if args.callback:
    shared.callback = args.callback
if args.apikey:
    shared.apikey = args.apikey
if args.pid:
    shared.pidfile = args.pid
if args.cycle:
    shared.cycle = float(args.cycle)
if args.socketport:
	shared.socket_port = args.socketport
		
shared.socket_port = int(shared.socket_port)

jeedom_utils.setshared.log_level(shared.log_level)

logging.info('Start demond')
logging.info('Log level : '+str(shared.log_level))
logging.info('Socket port : '+str(shared.socket_port))
logging.info('Socket host : '+str(shared.socket_host))
logging.info('PID file : '+str(shared.pidfile))
logging.info('Apikey : '+str(shared.apikey))
logging.info('Device : '+str(shared.device))
logging.info('Printer : '+str(shared.printer))
logging.info('Token : '+str(shared.token))
logging.info('Callback : '+str(shared.callback))
logging.info('Cycle : '+str(shared.cycle))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)	

try:
	jeedom_utils.write_pid(str(shared.pidfile))
	shared.JEEDOM_COM = jeedom_com(apikey = shared.apikey,url = shared.callback,cycle=shared.cycle)
	if not shared.JEEDOM_COM.test():
		logging.error('Network communication issues. Please fix your Jeedom network configuration.')
		shutdown()
	jeedom_socket = jeedom_socket(port=shared.socket_port,address=shared.socket_host)
	listen()
except Exception as e:
	logging.error('Fatal error : '+str(e))
	logging.info(traceback.format_exc())
	shutdown()
