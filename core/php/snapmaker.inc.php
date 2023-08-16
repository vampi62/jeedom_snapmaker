<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

require_once __DIR__  . '/../../../../core/php/core.inc.php';
/*
*
* Fichier d’inclusion si vous avez plusieurs fichiers de class ou 3rdParty à inclure
*
*/
if (!jeedom::apiAccess(init('apikey'), 'snapmaker')) {
	echo __('Vous n\'etes pas autorisé à effectuer cette action', __FILE__);
	die();
}
if (isset($_GET['test'])) {
	echo 'OK';
	die();
}
$result = json_decode(file_get_contents("php://input"), true);

log::add('snapmaker', 'debug', json_encode($result));
if (!is_array($result)) {
	die();
}

case 'status':
	$eqlogic->sendmessage('status',1);
	$this->getallvaluearray($info);
  break;
  case 'enclosure':
	$eqlogic->sendmessage('enclosure',1);
	$this->getallvaluearray($info);
  break;
  private function getallvaluearray($liste, $keyorigin = "") {
	$value_iniore = array("x","y","z","status"); // liste des valeurs a ne pas mettre a jour , x,y,z sont des valuer qui change regulierement et status n'est pas utilise donc pour eviter des ecriture inutile on ne le met pas a jour
	$eqlogic = $this->getEqLogic();
	foreach ($liste as $key => $value) {
	  if (is_array($value)) {
		$this->getallvaluearray($value,$keyorigin . "/" .$key);
	  } else {
		if (in_array($key, $value_iniore)) {
		  continue;
		}
		$element = $this->getCmd(null, $keyorigin . $key);
		if (is_object($element)) {
		  $eqlogic->checkAndUpdateCmd($keyorigin . $key, $value);
		} else {
		  log::add('snapmaker','debug',$keyorigin . $key . " - n'existe pas pour l'eqlogic " . $eqlogic->getName());
		}
	  }
	}
  }