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
			if message['apikey'] != _apikey:
				logging.error("Invalid apikey from socket : " + str(message))
				return
			try:
				logging.debug("Message received in socket : " + str(message['cmd']))
				logging.debug("Message received in socket : " + str(message['value']))
			except Exception as e:
				logging.error('Send command to demon error : '+str(e))
		time.sleep(_cycle)

payload={}
headers = {
  'Accept': 'application/json'
}
saved_token = "cbd3c2c7-fe60-4d6f-8366-7fd438d54e57"

def printer_connexion(name):
	while 1:
		time.sleep(1)
		if connect_to_printer:
			# ping printer device IP
			response = os.system("ping -c 1 " + _device)
			if response == 0:
				logging.debug("Printer is connected")
				jeedom_socket.send({'apikey':_apikey,'cmd':'printer_connected','value':'1'})
				printerconnect = requests.request("POST",'http://'+_device+':8080/api/v1/connect?token=' + _token, headers=headers, data=payload)
				logging.debug("Printer connect status code: " + str(printerconnect.status_code))
				printerconnect = json.loads(printerconnect.text)
				if _token == "":
					_token = printerconnect['token']
					jeedom_socket.send({'apikey':_apikey,'token': _token})
				logging.debug("Token : " + _token)
				logging.debug("Printer connect : " + str(printerconnect))
				while connect_to_printer:
					time.sleep(1)
					printerstatus = requests.request("GET",'http://'+_device+':8080/api/v1/status?token=' + _token, headers=headers, data=payload)
					logging.debug("Printer connect status code: " + str(printerstatus.status_code))
					printerstatus = json.loads(printerstatus.text)
					time.sleep(2.5)
					if printerstatus['module']["enclosure"]:
						printerenclosure = requests.request("GET",'http://'+_device+':8080/api/v1/enclosure?token=' + _token, headers=headers, data=payload)
						logging.debug("Printer connect status code: " + str(printerenclosure.status_code))
						printerenclosure = json.loads(printerenclosure.text)
					time.sleep(1.5)
					#JEEDOM_COM.send_change_immediate({'devices':{'wifi':default}})
					#if status.status_code == 200:
					#	break



			else:
				logging.debug("Printer is not connected")
				connect_to_printer = False
				jeedom_socket.send({'apikey':_apikey,'cmd':'printer_disconnected','value':'1'})




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
	logging.debug("Removing PID file " + str(_pidfile))
	try:
		os.remove(_pidfile)
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

JEEDOM_COM = ''
_log_level = "error"
_socket_port = 55009
_socket_host = 'localhost'
_device = 'auto'
_token = ''
_pidfile = '/tmp/snapmakerd.pid'
_apikey = ''
_callback = ''
_cycle = 0.3

connect_to_printer = False


parser = argparse.ArgumentParser(
    description='Desmond Daemon for Jeedom plugin')
parser.add_argument("--device", help="Device", type=str)
parser.add_argument("--token", help="Token", type=str)
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--callback", help="Callback", type=str)
parser.add_argument("--apikey", help="Apikey", type=str)
parser.add_argument("--cycle", help="Cycle to send event", type=str)
parser.add_argument("--pid", help="Pid file", type=str)
parser.add_argument("--socketport", help="Port for Zigbee server", type=str)
args = parser.parse_args()

if args.device:
	_device = args.device
if args.token:
	_token = args.token
if args.loglevel:
    _log_level = args.loglevel
if args.callback:
    _callback = args.callback
if args.apikey:
    _apikey = args.apikey
if args.pid:
    _pidfile = args.pid
if args.cycle:
    _cycle = float(args.cycle)
if args.socketport:
	_socket_port = args.socketport
		
_socket_port = int(_socket_port)

jeedom_utils.set_log_level(_log_level)

logging.info('Start demond')
logging.info('Log level : '+str(_log_level))
logging.info('Socket port : '+str(_socket_port))
logging.info('Socket host : '+str(_socket_host))
logging.info('PID file : '+str(_pidfile))
logging.info('Apikey : '+str(_apikey))
logging.info('Device : '+str(_device))
logging.info('Token : '+str(_token))
logging.info('Callback : '+str(_callback))
logging.info('Cycle : '+str(_cycle))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)	

try:
	jeedom_utils.write_pid(str(_pidfile))
	JEEDOM_COM = jeedom_com(apikey = _apikey,url = _callback,cycle=_cycle)
	if not JEEDOM_COM.test():
		logging.error('Network communication issues. Please fix your Jeedom network configuration.')
		shutdown()
	jeedom_socket = jeedom_socket(port=_socket_port,address=_socket_host)
	listen()
except Exception as e:
	logging.error('Fatal error : '+str(e))
	logging.info(traceback.format_exc())
	shutdown()
