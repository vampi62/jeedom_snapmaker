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

$elements = snapmaker::byType('snapmaker', true);
$snapmakerid = null;
for ($i = 0; $i < count($elements); $i++) {
	$id_objet = $elements[$i]->getId();
	if ($id_objet == $result['device']) {
		$snapmakerid = $elements[$i];
		break;
	}
}
if (!is_object($snapmakerid)) {
	log::add('snapmaker', 'debug', "Aucun snapmaker avec l'id " . $result['device']);
	die();
}
if (isset($result['token'])) {
	log::add('snapmaker', 'debug', "mise a jour du token pour " . $snapmakerid->getName());
	$oldtoken = $snapmakerid->getConfiguration('tokenapihttp', "none");
	log::add('snapmaker', 'debug', "oldtoken " . $oldtoken);
	log::add('snapmaker', 'debug', "newtoken " . $result['token']);
	if ($result['token'] != $oldtoken) {
		log::add('snapmaker', 'debug', "mise a jour du token pour " . $snapmakerid->getName());
		$snapmakerid->setConfiguration('tokenapihttp', $result['token']);
	}
	unset($result['token']);
}
unset($result['device']);
unset($result['apikey']);

if (isset($result['status'])) {
	$snapmakerid->checkAndUpdateCmd('printStatus', strval($result['status']));
	unset($result['status']);
}
if (isset($result['statusconnect'])) {
	$snapmakerid->checkAndUpdateCmd('status', strval($result['statusconnect']));
	unset($result['statusconnect']);
}
if (isset($result['returnstatus'])) {
	log::add('snapmaker', 'debug', "mise a jour du status pour " . $snapmakerid->getName() . " - " . $result['returnstatus']);
	unset($result['returnstatus']);
}
if (isset($result['returncmd'])) {
	log::add('snapmaker', 'debug', "mise a jour du cmd pour " . $snapmakerid->getName() . " - " . $result['returncmd']);
	unset($result['returncmd']);
}
if (isset($result['returnvalue'])) {
	log::add('snapmaker', 'debug', "mise a jour du value pour " . $snapmakerid->getName() . " - " . $result['returnvalue']);
	unset($result['returnvalue']);
}

function getallvaluearray($snapmakerid,$liste, $keyorigin = "") {
	$value_iniore = array("x","y","z","offsetX","offsetY"); // liste des valeurs a ne pas mettre a jour , x,y,z sont des valuer qui change regulierement et status n'est pas utilise donc pour eviter des ecriture inutile on ne le met pas a jour
	foreach ($liste as $key => $value) {
		if (is_array($value)) {
			getallvaluearray($snapmakerid,$value,$keyorigin . "/" .$key);
		} else {
			if (in_array($key, $value_iniore)) {
				continue;
			}
			$element = $snapmakerid->getCmd(null, $key);
			if (is_object($element)) {
				if (is_bool($value)) {
					$value = $value ? 'true' : 'false';
				}
				$snapmakerid->checkAndUpdateCmd($key, strval($value));
			} else {
				log::add('snapmaker','debug',$keyorigin . "/" .$key . " - n'existe pas pour l'eqlogic " . $snapmakerid->getName());
			}
		}
	}
}
getallvaluearray($snapmakerid,$result);