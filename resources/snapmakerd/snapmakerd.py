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
from requests.exceptions import ConnectTimeout

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
				payload={}
				headers = {'Accept': 'application/json'}
				printerreturnjson = {}
				commandnotfound = False
				if message['cmd'] == 'connect':
					shared.connect_to_printer = True
				elif message['cmd'] == 'disconnect':
					shared.connect_to_printer = False
					time.sleep(3)
					if shared.printerconnected:
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/disconnect', headers=headers, data=payload, timeout=3)
				elif shared.printerconnected:
					if message['cmd'] == 'settempnozzle':
						payload = {'token': shared.token,"nozzleTemp": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/override_nozzle_temperature', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'settempbed':
						payload = {'token': shared.token,"heatedBedTemp": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/override_bed_temperature', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'setspeed':
						payload = {'token': shared.token,"workSpeed": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/override_work_speed', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'setzoffset':
						payload = {'token': shared.token,"zOffset": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/override_z_offset', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'pause':
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/pause_print', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'startprintfile':
						filename = message['value']
						payload = {'token': shared.token, 'type': '3DP'}
						file = open(filename, 'rb')
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/prepare_print', data=payload, files={'file': (filename, file)}, timeout=45)
						logging.debug("code : "+str(printerreturn.status_code))
						if printerreturn.status_code == 200:
							printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/start_print', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'sendfile':
						filename = message['value']
						payload = {'token': shared.token}
						file = open(filename, 'rb')
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/upload', data=payload,files={'file': (filename, file)}, timeout=45)
						logging.debug("code : "+str(printerreturn.status_code))
					elif message['cmd'] == 'stop':
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/stop_print', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'resume':
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/resume_print', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'reload':
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/filament_load', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'unload':
						payload = {'token': shared.token}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/filament_unload', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'setpauseifopen':
						payload = {'token': shared.token,"isDoorEnabled": True}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/enclosure', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'unsetpauseifopen':
						payload = {'token': shared.token,"isDoorEnabled": False}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/enclosure', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'setlight':
						payload = {'token': shared.token,"led": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/enclosure', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'setfan':
						payload = {'token': shared.token,"fan": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/enclosure', headers=headers, data=payload, timeout=3)
					elif message['cmd'] == 'execcomande':
						payload = {'token': shared.token,"code": message['value']}
						printerreturn = requests.request("POST",'http://'+shared.printer+':8080/api/v1/execute_code', headers=headers, data=payload, timeout=3)
					#override_laser_power
					#laserPower
					else:
						commandnotfound = True
						printerreturnjson['returncmd'] = message['cmd']
						printerreturnjson['returnvalue'] = message['value']
				else:
					commandnotfound = True
					printerreturnjson['returncmd'] = message['cmd']
					printerreturnjson['returnvalue'] = message['value']
				printerreturnjson['returnstatus'] = not commandnotfound
				printerreturnjson["apikey"] = shared.apikey
				printerreturnjson['device'] = shared.device
				shared.JEEDOM_COM.send_change_immediate(printerreturnjson)
			except ConnectTimeout:
				shared.printerconnected = False
				logging.error('Printer connexion timeout')
			except Exception as e:
				logging.error('Send command to demon error : '+str(e))
		time.sleep(shared.cycle)


saved_token = "cbd3c2c7-fe60-4d6f-8366-7fd438d54e57"

def printer_connexion(name):
	payload={}
	headers = {'Accept': 'application/json'}
	while 1:
		time.sleep(1)
		if shared.connect_to_printer:
			# ping printer device IP
			response = os.system("ping -c 1 " + shared.printer)
			if response == 0:
				try:
					printerconnecthttp = requests.request("POST",'http://'+shared.printer+':8080/api/v1/connect?token=' + shared.token, headers=headers, data=payload, timeout=3)
					printerconnectjson = json.loads(printerconnecthttp.text)
					if shared.token == "":
						shared.token = printerconnectjson['token']
					printerconnectjson["apikey"] = shared.apikey
					printerconnectjson['device'] = shared.device
					printerconnectjson["token"] = shared.token
					if printerconnecthttp.status_code == 200:
						printerconnectjson['statusconnect'] = "1"
						shared.printerconnected = True
					else:
						printerconnectjson['statusconnect'] = "0"
						shared.printerconnected = False
					shared.JEEDOM_COM.send_change_immediate(printerconnectjson)
					while shared.printerconnected:
						time.sleep(0.5)
						printerstatushttp = requests.request("GET",'http://'+shared.printer+':8080/api/v1/status?token=' + shared.token, headers=headers, data=payload, timeout=3)
						printerstatusjson = json.loads(printerstatushttp.text)
						time.sleep(1.5)
						if printerstatusjson['moduleList']["enclosure"]:
							printerenclosurehttp = requests.request("GET",'http://'+shared.printer+':8080/api/v1/enclosure?token=' + shared.token, headers=headers, data=payload, timeout=3)
							printerenclosurejson = json.loads(printerenclosurehttp.text)
							printerstatusjson["enclosure"] = printerenclosurejson
						time.sleep(1)
						printerstatusjson["apikey"] = shared.apikey
						printerstatusjson['device'] = shared.device
						shared.JEEDOM_COM.send_change_immediate(printerstatusjson)
						if printerstatushttp.status_code != 200 or not shared.connect_to_printer:
							shared.printerconnected = False
				except ConnectTimeout:
					shared.printerconnected = False
					logging.error('Printer connexion timeout')
				except Exception as e:
					logging.error('Send command to demon error : '+str(e))
			shared.connect_to_printer = False
			shared.JEEDOM_COM.send_change_immediate({'apikey':shared.apikey,'device':shared.device,'statusconnect':'0'})


def listen():
	jeedom_socket.open()
	logging.info("Start listening...")
	shared.JEEDOM_COM.send_change_immediate({'apikey':shared.apikey,'device':shared.device,'statusconnect':'0'})
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
	jeedom_socket.send_change_immediate({'apikey':shared.apikey,'statusconnect':'0'})
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
	if shared.token == "none":
		shared.token = saved_token
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

jeedom_utils.set_log_level(shared.log_level)

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
