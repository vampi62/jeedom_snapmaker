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

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class snapmaker extends eqLogic {
  /*     * *************************Attributs****************************** */

  /*
  * Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
  * Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
  public static $_widgetPossibility = array();
  */

  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration du plugin
  * Exemple : "param1" & "param2" seront cryptés mais pas "param3"
  public static $_encryptConfigKey = array('param1', 'param2');
  */

  /*     * ***********************Methode static*************************** */

  /*
  * Fonction exécutée automatiquement toutes les minutes par Jeedom
  */
  public static function cron() {
    foreach (self::byType('snapmaker', true) as $snapmaker) { //parcours tous les équipements actifs du plugin
      if ($snapmaker->getCmd(null, 'autoconnect')->execCmd() == "1") {
        if ($snapmaker->getCmd(null, 'statusconnect')->execCmd() == "0") {
          $cmd = $snapmaker->getCmd(null, 'connect');
          if (is_object($cmd)) {
            $cmd->execCmd(); // connexion à l'imprimante si autoconnect = 1 et statusconnect = 0
          }
        }
      }
      if ($snapmaker->getCmd(null, 'autoshutdown')->execCmd() == "1") {
        if ($snapmaker->getCmd(null, 'statusconnect')->execCmd() == "1") {
          if ($snapmaker->getCmd(null, 'printStatus')->execCmd() == "IDLE") {
            $snapmaker->getCmd(null, 'disconnect')->execCmd();
            $cmdalimoff = cmd::byId(str_replace("#","",$snapmaker->getConfiguration('offalim','')));
            if (is_object($cmdalimoff)) {
              sleep(5);
              $cmdalimoff->execCmd();
            }
            $snapmaker->checkAndUpdateCmd('autoshutdown', '0');
          }
        }
      }
    }
  }

  /*
  * Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
  public static function cron5() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
  public static function cron10() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  */
  /*
  public static function cron30() {
    foreach (self::byType('snapmaker', true) as $snapmaker) { //parcours tous les équipements actifs du plugin
      $cmd = $snapmaker->getCmd(null, 'refresh');
      if (!is_object($cmd)) {
        continue;
      }
      $cmd->execCmd();
    }
  }
  */

  /*
  * Fonction exécutée automatiquement toutes les heures par Jeedom
  public static function cronHourly() {}
  */

  /*
  * Fonction exécutée automatiquement tous les jours par Jeedom
  public static function cronDaily() {}
  */

  /*     * *********************Méthodes d'instance************************* */

  // Fonction exécutée automatiquement avant la création de l'équipement
  private function check_port_dispo($portToCheck) {
    $command = "sudo netstat -tuln | grep 'LISTEN' | awk '{print $4}' | cut -d ':' -f 2"; // Commande pour lister les ports en écoute
    $output = [];
    $returnVar = 0;
    exec($command, $output, $returnVar);
    if ($returnVar === 0) {
      return $output;
    } else {
      log::add('snapmaker', 'debug', 'Erreur lors de la recherche de port libre');
      return [];
    }
  }
  public function preInsert() {
    $this->setConfiguration('cycle', '0.3');
    $defaut_port_socket = 12100;
    $list_port_used = $this->check_port_dispo($defaut_port_socket + $i);
    for ($i = 0; $i < 99; $i++) {
      log::add('snapmaker', 'debug', 'Test port : ' . strval($defaut_port_socket + $i));
      if (!in_array(strval($defaut_port_socket + $i), $list_port_used)) {
        $this->setConfiguration('socketport', $defaut_port_socket + $i);
        break;
      }
    }
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
    if ($this->getConfiguration('socketport','') != '') {
      self::deamon_start_instance($this);
    }
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
    if (!is_object(cmd::byId(str_replace("#","",$this->getConfiguration('statusalim',''))))) {
      $this->setConfiguration('statusalim', '');
    }
    if (!is_object(cmd::byId(str_replace("#","",$this->getConfiguration('onalim',''))))) {
      $this->setConfiguration('onalim', '');
    }
    if (!is_object(cmd::byId(str_replace("#","",$this->getConfiguration('offalim',''))))) {
      $this->setConfiguration('offalim', '');
    }
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
    $this->sendmessage("updateip", $this->getConfiguration("adresseip", "none"));
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
  }

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    $this->create_element('refresh','Rafraichir','action','other');
    $this->create_element('pause'  ,'pause'     ,'action','other');
    $this->create_element('start'  ,'start'     ,'action','message');
    $this->create_element('stop'   ,'stop'      ,'action','other');
    $this->create_element('resume' ,'resume'    ,'action','other');

    $this->create_element('connect'   ,'connect'   ,'action','other');
    $this->create_element('disconnect','disconnect','action','other');

    $this->create_element('setautoconnect'  ,'setautoconnect'  ,'action','other');
    $this->create_element('unsetautoconnect','unsetautoconnect','action','other');

    $this->create_element('setautoshutdown'  ,'setautoshutdown'  ,'action','other');
    $this->create_element('unsetautoshutdown','unsetautoshutdown','action','other');

    $this->create_element('execcomande','execcomande','action','message');
    $this->create_element('sendfile'   ,'sendfile'   ,'action','message');

    $this->create_element('reload','reload','action','other');
    $this->create_element('unload','unload','action','other');

    $this->create_element('setpauseifopen'  ,'setpauseifopen'  ,'action','other');
    $this->create_element('unsetpauseifopen','unsetpauseifopen','action','other');
    
    $this->create_element('settempnozzle','settempnozzle','action','message');
    $this->create_element('settempbed'   ,'settempbed'   ,'action','message');
    $this->create_element('setspeed'     ,'setspeed'     ,'action','message');
    $this->create_element('setzoffset'   ,'setzoffset'   ,'action','message');
    $this->create_element('settempnozzle1','settempnozzle1','action','message');
    $this->create_element('settempnozzle2','settempnozzle2','action','message');
    $this->create_element('setlight'     ,'setlight'     ,'action','other');
    $this->create_element('setfan'       ,'setfan'       ,'action','other');
    $this->create_element('unsetlight'   ,'unsetlight'   ,'action','other');
    $this->create_element('unsetfan'     ,'unsetfan'     ,'action','other');
    $this->create_element('saveworkSpeed','saveworkSpeed','info'  ,'string');
    $this->create_element('returnstatus' ,'returnstatus' ,'info'  ,'string');
    
    $this->create_element('autoconnect'               ,'autoconnect'               ,'info','string');
    $this->create_element('autoshutdown'              ,'autoshutdown'              ,'info','string');
    $this->create_element('filelist'                  ,'filelist'                  ,'info','string');
    $this->create_element('statusconnect'             ,'statusconnect'             ,'info','string');
    $this->create_element('homed'                     ,'homed'                     ,'info','string');
    $this->create_element('toolHead'                  ,'toolHead'                  ,'info','string');
    $this->create_element('headType'                  ,'headType'                  ,'info','string');
    $this->create_element('hasEnclosure'              ,'hasEnclosure'              ,'info','string');
    $this->create_element('laserCamera'               ,'laserCamera'               ,'info','string');
    $this->create_element('laserPower'                ,'laserPower'                ,'info','string');
    $this->create_element('laserFocalLength'          ,'laserFocalLength'          ,'info','string');
    $this->create_element('laser10WErrorState'        ,'laser10WErrorState'        ,'info','string');
    $this->create_element('nozzleTemperature1'        ,'nozzleTemperature1'        ,'info','string');
    $this->create_element('nozzleTargetTemperature1'  ,'nozzleTargetTemperature1'  ,'info','string');
    $this->create_element('nozzleTemperature2'        ,'nozzleTemperature2'        ,'info','string');
    $this->create_element('nozzleTargetTemperature2'  ,'nozzleTargetTemperature2'  ,'info','string');
    $this->create_element('nozzleTemperature'         ,'nozzleTemperature'         ,'info','string');
    $this->create_element('nozzleTargetTemperature'   ,'nozzleTargetTemperature'   ,'info','string');
    $this->create_element('heatedBedTemperature'      ,'heatedBedTemperature'      ,'info','string');
    $this->create_element('heatedBedTargetTemperature','heatedBedTargetTemperature','info','string');
    $this->create_element('isFilamentOut'             ,'isFilamentOut'             ,'info','string');
    $this->create_element('printStatus'               ,'printStatus'               ,'info','string');
    $this->create_element('fileName'                  ,'fileName'                  ,'info','string');
    $this->create_element('totalLines'                ,'totalLines'                ,'info','string');
    $this->create_element('estimatedTime'             ,'estimatedTime'             ,'info','string');
    $this->create_element('currentLine'               ,'currentLine'               ,'info','string');
    $this->create_element('progress'                  ,'progress'                  ,'info','string');

    //$this->set_limit_element('progress',0,100);
    $this->create_element('elapsedTime'               ,'elapsedTime'               ,'info','string');
    $this->create_element('remainingTime'             ,'remainingTime'             ,'info','string');
    $this->create_element('enclosure'                 ,'enclosure'                 ,'info','string');
    $this->create_element('rotaryModule'              ,'rotaryModule'              ,'info','string');
    $this->create_element('emergencyStopButton'       ,'emergencyStopButton'       ,'info','string');
    $this->create_element('airPurifier'               ,'airPurifier'               ,'info','string');
    $this->create_element('isEnclosureDoorOpen'       ,'isEnclosureDoorOpen'       ,'info','string');
    $this->create_element('doorSwitchCount'           ,'doorSwitchCount'           ,'info','string');
    $this->create_element('stopIfEnclosureDoorOpen'   ,'stopIfEnclosureDoorOpen'   ,'info','string');
    $this->create_element('isReady'                   ,'isReady'                   ,'info','string');
    $this->create_element('isDoorEnabled'             ,'isDoorEnabled'             ,'info','string');
    $this->create_element('led'                       ,'led'                       ,'info','string');
    $this->create_element('fan'                       ,'fan'                       ,'info','string');
    $this->create_element('offsetZ'                   ,'offsetZ'                   ,'info','string');
    $path = dirname(__FILE__) . '/../../data/' . $this->getId();
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    $path = dirname(__FILE__) . '/../../data/' . $this->getId();
    if (file_exists($path)) {
      $objects = scandir($path);
      foreach ($objects as $object) {
        if ($object != '.' && $object != '..') {
          unlink($path.'/'.$object);
        }
      }
      rmdir($path);
    }
    self::deamon_stop_instance($this);
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  private function set_limit_element($cmd,$mini,$maxi){
    $element = $this->getCmd(null, $cmd);
    if (is_object($element)) {
      if (($element->getConfiguration('maxValue', "") != $maxi) || ($element->getConfiguration('minValue', "") != $mini)) {
        $element->setConfiguration('maxValue', $maxi);
        $element->setConfiguration('minValue', $mini);
        $element->save();
      }
    }
  }
  private function create_element($newcmd,$newname,$newtype,$newsubtype,$newunit = "",$newtemplate = 'default'){
    $newelement = $this->getCmd(null, $newcmd);
    if (!is_object($newelement)) {
      $newelement = new snapmakerCmd();
      $newelement->setName(__($newname, __FILE__));
    }
    $newelement->setEqLogic_id($this->getId());
    $newelement->setLogicalId($newcmd);
    $newelement->setType($newtype);
    $newelement->setSubType($newsubtype);
    if ($newtype == 'info') {
      $newelement->setGeneric_type('GENERIC_INFO');
    } else {
      $newelement->setGeneric_type('GENERIC_ACTION');
    }
    if ($newunit != "") {
      $newelement->setUnite($newunit);
    }
    $newelement->save();
  }
  /*
  * Permet de crypter/décrypter automatiquement des champs de configuration des équipements
  * Exemple avec le champ "Mot de passe" (password)
  public function decrypt() {
    $this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
  }
  public function encrypt() {
    $this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
  }
  */

  /*
  * Permet de modifier l'affichage du widget (également utilisable par les commandes)
  public function toHtml($_version = 'dashboard') {}
  */

  /*
  * Permet de déclencher une action avant modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function preConfig_param3( $value ) {
    // do some checks or modify on $value
    return $value;
  }
  */

  /*
  * Permet de déclencher une action après modification d'une variable de configuration du plugin
  * Exemple avec la variable "param3"
  public static function postConfig_param3($value) {
    // no return value
  }
  */
  public function sendmessage($cmdsend,$valuesend) {
    $value = json_encode(array('apikey' => jeedom::getApiKey('snapmaker'), 'cmd' => $cmdsend, 'value' => $valuesend));
    log::add('snapmaker', 'debug', 'Envoi : ' . $value);
    $deamon_info = self::deamon_info();
    if ($deamon_info['state'] != 'ok') {
      return;
    }
    $socket = socket_create(AF_INET, SOCK_STREAM, 0);
    socket_connect($socket, '127.0.0.1', $this->getConfiguration("socketport", "12200"));
    socket_write($socket, $value, strlen($value));
    socket_close($socket);
  }


  public static function deamon_info() {
    $return = array();
    $return['log'] = 'snapmaker';
    $return['state'] = 'ok';
    $return['launchable'] = 'ok';
    $elements = self::byType('snapmaker', true);
    for ($i = 0; $i < count($elements); $i++) {
      $info = self::deamon_info_instance($elements[$i]);
      if ($info['state'] != 'ok') {
        $return['state'] = $info['state'];
      }
      if ($info['launchable'] != 'ok') {
        $return['launchable'] = $info['launchable'];
        $return['launchable_message'] = $info['launchable_message'];
      }
    }
    return $return;
  }

  public static function deamon_info_instance($_instance) {
    $return = array();
    $return['log'] = 'snapmaker';
    $return['state'] = 'nok';
    $id_objet = $_instance->getId();
    $pid_file = jeedom::getTmpFolder('snapmaker') . '/deamon_' . $id_objet . '.pid';
    if (file_exists($pid_file)) {
      $pid = trim(file_get_contents($pid_file));
      if (is_numeric($pid) && posix_getsid($pid)) {
        $return['state'] = 'ok';
      } else {
        shell_exec(system::getCmdSudo() . 'rm -rf ' . $pid_file . ' 2>&1 > /dev/null;rm -rf ' . $pid_file . ' 2>&1 > /dev/null;');
      }
    }
    $return['launchable'] = 'ok';
    if ($_instance->getConfiguration("socketport", "") == '') {
      $return['launchable'] = 'nok';
      $return['launchable_message'] = __('Le port du socket n\'est pas configuré pour : ' . $_instance->getName(), __FILE__);
    }
    return $return;
  }

  public static function deamon_start($_auto = false) {
    $elements = self::byType('snapmaker', true);
    for ($i = 0; $i < count($elements); $i++) {
      if ($_auto) {
        $infos = self::deamon_info_instance($elements[$i]);
        if ($infos['state'] == 'ok') {
          continue;
        }
      }
      self::deamon_start_instance($elements[$i]);
    }
    return true;
  }

  public static function deamon_start_instance($_instance) {
    $id_objet = $_instance->getId();
    self::deamon_stop_instance($_instance);
    $deamon_info = self::deamon_info_instance($_instance);
    if ($deamon_info['launchable'] != 'ok') {
      throw new Exception(__($deamon_info['launchable_message'], __FILE__));
    }
    $snapmaker_path = realpath(__DIR__ . '/../../resources/snapmakerd');
    $cmd = '/usr/bin/python3 ' . $snapmaker_path . '/snapmakerd.py';
    $cmd .= ' --device ' . $id_objet;
    $cmd .= ' --printer ' . $_instance->getConfiguration("adresseip", "none");
    $cmd .= ' --token ' . $_instance->getConfiguration("tokenapihttp", "none");
    $cmd .= ' --loglevel ' . log::convertLogLevel(log::getLogLevel('snapmaker'));
    $cmd .= ' --socketport ' . $_instance->getConfiguration("socketport", "12200");
    $cmd .= ' --callback ' . network::getNetworkAccess('internal', 'proto:127.0.0.1:port:comp') . '/plugins/snapmaker/core/php/snapmaker.inc.php';
    $cmd .= ' --cycle ' . $_instance->getConfiguration("cycle", "0.3");
    $cmd .= ' --apikey ' . jeedom::getApiKey('snapmaker');
    $cmd .= ' --pid ' . jeedom::getTmpFolder('snapmaker') . '/deamon_' . $id_objet . '.pid';
    log::add('snapmaker', 'info', 'Lancement démon snapmakerd : ' . $cmd);
    exec($cmd . ' >> ' . log::getPathToLog('snapmakerd_' . $id_objet) . ' 2>&1 &');
    config::save('lastDeamonLaunchTime_' . $id_objet, date('Y-m-d H:i:s'), 'snapmaker');
    return true;
  }

  public static function deamon_stop() {
    $elements = self::byType('snapmaker', true);
    for ($i = 0; $i < count($elements); $i++) {
      $infos = self::deamon_info_instance($elements[$i]);
      if ($infos['launchable'] == 'ok') {
        self::deamon_stop_instance($elements[$i]);
      }
    }
    system::kill('snapmakerd.py');
  }

  public static function deamon_stop_instance($_instance) {
    $id_objet = $_instance->getId();
    $pid_file = jeedom::getTmpFolder('snapmaker') . '/deamon' . $id_objet . '.pid';
    if (file_exists($pid_file)) {
      $pid = intval(trim(file_get_contents($pid_file)));
      system::kill($pid);
    }
    system::fuserk($_instance->getConfiguration("socketport", "12200"));
    sleep(1);
  }

  /*     * **********************Getteur Setteur*************************** */
  public function toHtml($_version = 'dashboard') {
    $replace = $this->preToHtml($_version);
    if (!is_array($replace)) {
      return $replace;
    }
    $version = jeedom::versionAlias($_version);
    foreach ($this->getCmd('info') as $cmd) {
      if (!is_object($cmd)) {
        continue;
      }
      $replace['#' . $cmd->getLogicalId() . '#'] = $cmd->execCmd();
      $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
      $replace['#' . $cmd->getLogicalId() . '_valueDate#']= date('d-m-Y H:i:s',strtotime($cmd->getValueDate()));
      $replace['#' . $cmd->getLogicalId() . '_collectDate#'] =date('d-m-Y H:i:s',strtotime($cmd->getCollectDate()));
      $replace['#' . $cmd->getLogicalId() . '_updatetime#'] =date('d-m-Y H:i:s',strtotime( $this->getConfiguration('updatetime')));
      $replace['#lastCommunication#'] =date('d-m-Y H:i:s',strtotime($this->getStatus('lastCommunication')));
      $replace['#numberTryWithoutSuccess#'] = $this->getStatus('numberTryWithoutSuccess', 0);
      if ($cmd->getIsHistorized() == 1) {
        $replace['#' . $cmd->getLogicalId() . '_history#'] = 'history cursor';
      }
    }
    foreach ($this->getCmd('action') as $cmd) {
      if (!is_object($cmd)) {
        continue;
      }
      $replace['#' . $cmd->getLogicalId() . '_id#'] = $cmd->getId();
      if ($cmd->getConfiguration('listValue', '') != '') {
        $listOption = '';
        $elements = explode(';', $cmd->getConfiguration('listValue', ''));
        $foundSelect = false;
        foreach ($elements as $element) {
          list($item_val, $item_text) = explode('|', $element);
          //$coupleArray = explode('|', $element);
          $cmdValue = $cmd->getCmdValue();
          if (is_object($cmdValue) && $cmdValue->getType() == 'info') {
            if ($cmdValue->execCmd() == $item_val) {
              $listOption .= '<option value="' . $item_val . '" selected>' . $item_text . '</option>';
              $foundSelect = true;
            } else {
              $listOption .= '<option value="' . $item_val . '">' . $item_text . '</option>';
            }
          } else {
            $listOption .= '<option value="' . $item_val . '">' . $item_text . '</option>';
          }
        }
        //if (!$foundSelect) {
        //	$listOption = '<option value="">Aucun</option>' . $listOption;
        //}
        //$replace['#listValue#'] = $listOption;
        $replace['#' . $cmd->getLogicalId() . '_id_listValue#'] = $listOption;
        $replace['#' . $cmd->getLogicalId() . '_listValue#'] = $listOption;
      }
    }
    $parameters = $this->getDisplay('parameters');
    if (is_array($parameters)) {
      foreach ($parameters as $key => $value) {
        $replace['#' . $key . '#'] = $value;
      }
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('onalim')));
    if (is_object($cmd)) {
      $replace['#onalim_id#'] = $cmd->getId();
    } else {
      $replace['#onalim_id#'] = "-1";
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('offalim')));
    if (is_object($cmd)) {
      $replace['#offalim_id#'] = $cmd->getId();
    } else {
      $replace['#offalim_id#'] = "-1";
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('statusalim')));
    if (is_object($cmd)) {
      $replace['#aliminfo#'] = $cmd->execCmd();
      $replace['#aliminfo_id#'] = $cmd->getId();
      $replace['#aliminfo_valueDate#'] = date('d-m-Y H:i:s',strtotime($cmd->getValueDate()));
      $replace['#aliminfo_collectDate#'] = date('d-m-Y H:i:s',strtotime($cmd->getCollectDate()));
      $replace['#aliminfo_updatetime#'] = date('d-m-Y H:i:s',strtotime( $this->getConfiguration('updatetime')));
    } else {
      $replace['#aliminfo#'] = "-1";
      $replace['#aliminfo_id#'] = "-1";
      $replace['#aliminfo_valueDate#'] = date('d-m-Y H:i:s',strtotime($this->getConfiguration('updatetime')));
      $replace['#aliminfo_collectDate#'] = date('d-m-Y H:i:s',strtotime($this->getConfiguration('updatetime')));
      $replace['#aliminfo_updatetime#'] = date('d-m-Y H:i:s',strtotime($this->getConfiguration('updatetime')));
    }
    $replace['#heightfilelist#'] = strval(intval($replace['#height#'])-500);
    $replace['#widthfilelist#'] = strval(intval($replace['#width#']));
    $replace['#heightmenu#'] = strval(intval($replace['#height#'])-50);
    $replace['#widthmenu#'] = strval(intval($replace['#width#']));
    $widgetType = getTemplate('core', $version, 'box', __CLASS__);
		return $this->postToHtml($_version, template_replace($replace, $widgetType));
	}
}

class snapmakerCmd extends cmd {
  /*     * *************************Attributs****************************** */

  /*
  public static $_widgetPossibility = array();
  */

  /*     * ***********************Methode static*************************** */


  /*     * *********************Methode d'instance************************* */

  /*
  * Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
  public function dontRemoveCmd() {
    return true;
  }
  */

  // Exécution d'une commande
  public function execute($_options = array()) {
    $eqlogic = $this->getEqLogic(); //récupère l'éqlogic de la commande $this
    switch ($this->getLogicalId()) { //vérifie le logicalid de la commande
      case 'refresh':
        function convert($size) {
          $unit = array('o', 'Ko', 'Mo', 'Go', 'To', 'Po');
          return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
        }
        $filelist = array();
        $files = array_diff(scandir(dirname(__FILE__) . '/../../data/' . $eqlogic->getId()), array('.', '..'));
        foreach ($files as $file) {
          $filedir = dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $file;
          $filecont = fopen($filedir, "r");
          // lire les 100 premire ligne a la recherche d'un des éléments suivant : ;thumbnail, ;file_total_lines, ;estimated_time.
          // Si trouvé, on récupère le texte de la ligne et on le stocke dans une variable
          $thumbnail = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEBLAEsAAD/4QBWRXhpZgAATU0AKgAAAAgABAEaAAUAAAABAAAAPgEbAAUAAAABAAAARgEoAAMAAAABAAIAAAITAAMAAAABAAEAAAAAAAAAAAEsAAAAAQAAASwAAAAB/+0ALFBob3Rvc2hvcCAzLjAAOEJJTQQEAAAAAAAPHAFaAAMbJUccAQAAAgAEAP/hDIFodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvADw/eHBhY2tldCBiZWdpbj0n77u/JyBpZD0nVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkJz8+Cjx4OnhtcG1ldGEgeG1sbnM6eD0nYWRvYmU6bnM6bWV0YS8nIHg6eG1wdGs9J0ltYWdlOjpFeGlmVG9vbCAxMS44OCc+CjxyZGY6UkRGIHhtbG5zOnJkZj0naHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyc+CgogPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9JycKICB4bWxuczp0aWZmPSdodHRwOi8vbnMuYWRvYmUuY29tL3RpZmYvMS4wLyc+CiAgPHRpZmY6UmVzb2x1dGlvblVuaXQ+MjwvdGlmZjpSZXNvbHV0aW9uVW5pdD4KICA8dGlmZjpYUmVzb2x1dGlvbj4zMDAvMTwvdGlmZjpYUmVzb2x1dGlvbj4KICA8dGlmZjpZUmVzb2x1dGlvbj4zMDAvMTwvdGlmZjpZUmVzb2x1dGlvbj4KIDwvcmRmOkRlc2NyaXB0aW9uPgoKIDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PScnCiAgeG1sbnM6eG1wTU09J2h0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8nPgogIDx4bXBNTTpEb2N1bWVudElEPmFkb2JlOmRvY2lkOnN0b2NrOmMxNWJkMGViLWJlYmMtNDZjMC1iM2Q5LTE0OWYxOGUxNTE4NjwveG1wTU06RG9jdW1lbnRJRD4KICA8eG1wTU06SW5zdGFuY2VJRD54bXAuaWlkOjI5NjIwODdiLThlNGUtNDlkOC04NTI2LTYwODZkOTIxZTdiNDwveG1wTU06SW5zdGFuY2VJRD4KIDwvcmRmOkRlc2NyaXB0aW9uPgo8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAo8P3hwYWNrZXQgZW5kPSd3Jz8+/9sAQwAFAwQEBAMFBAQEBQUFBgcMCAcHBwcPCwsJDBEPEhIRDxERExYcFxMUGhURERghGBodHR8fHxMXIiQiHiQcHh8e/8AACwgBaAFoAQERAP/EABwAAQEBAQEBAQEBAAAAAAAAAAAGBQQDAgEHCP/EAEIQAAEDAgIFCAcFBwUBAQAAAAABAgMEEQUGEiE1krETFjFBUVRyghQiU2FkceEyc4GjwRUjQlKRk9EzNGKh8ENj/9oACAEBAAA/AP8AZYAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA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';
          $file_total_lines = 'unknown';
          $estimated_time = 'unknown';
          $header_type = 'unknown';
          $tool_head = 'unknown';
          $is_rotate = 'unknown';
          for ($i = 0; $i < 100; $i++) {
            $line = fgets($filecont);
            if (strpos($line, ';thumbnail:') !== false) {
              $thumbnail = substr($line, strpos($line, ';thumbnail:') + strlen(';thumbnail:') + 1); // image en base64
              $thumbnail = str_replace(array("\r", "" . PHP_EOL), '', $thumbnail);
            }
            if (strpos($line, ';file_total_lines:') !== false) {
              $segments = explode(":", $line);
              $file_total_lines = $segments[1]; // nombre de ligne du fichier
              $file_total_lines = str_replace(array("\r", ' ', "" . PHP_EOL), '', $file_total_lines);

            }
            if (strpos($line, ';estimated_time(s):') !== false) {
              $segments = explode(":", $line);
              $estimated_time = $segments[1]; // temps estimé en seconde
              $estimated_time = str_replace(array("\r", ' ', "" . PHP_EOL), '', $estimated_time);
            }
            if (strpos($line, ';TIME:') !== false) {
              $segments = explode(":", $line);
              $estimated_time = $segments[1]; // temps estimé en seconde
              $estimated_time = str_replace(array("\r", ' ', "" . PHP_EOL), '', $estimated_time);
            }
            if (strpos($line, ';header_type:') !== false) {
              $segments = explode(":", $line);
              $header_type = $segments[1]; // type de fichier
              $header_type = str_replace(array("\r", ' ', "" . PHP_EOL), '', $header_type);
            }
            if (strpos($line, ';tool_head:') !== false) {
              $segments = explode(":", $line);
              $tool_head = $segments[1]; // type de fichier
              $tool_head = str_replace(array("\r", ' ', "" . PHP_EOL), '', $tool_head);
            }
            if (strpos($line, ';is_rotate:') !== false) {
              $segments = explode(":", $line);
              $is_rotate = $segments[1]; // type de fichier
              $is_rotate = str_replace(array("\r", ' ', "" . PHP_EOL), '', $is_rotate);
            }
          }
          fclose($filecont);
          $filelist[] = $file . '-:-' . convert(filesize($filedir)) . '-:-' . date("Y-m-d H:i:s", filemtime($filedir)) . '-:-' . $header_type . '-:-' . $file_total_lines . '-:-' . $estimated_time . '-:-' . $thumbnail . '-:-' . $tool_head . '-:-' . $is_rotate;
        }
        $filelist = implode("-!-", $filelist);
        $eqlogic->checkAndUpdateCmd('filelist', $filelist);
      break;
      case 'connect':
        $eqlogic->sendmessage('connect',1);
      break;
      case 'disconnect':
        $eqlogic->sendmessage('disconnect',1);
      break;
      case 'settempnozzle':
        $eqlogic->sendmessage('settempnozzle',$_options['message']);
      break;
      case 'settempnozzle1':
        $eqlogic->sendmessage('execcomande', "M104 T0 S" . $_options['message']);
      break;
      case 'settempnozzle2':
        $eqlogic->sendmessage('execcomande',"M104 T1 S" . $_options['message']);
      break;
      case 'settempbed':
        $eqlogic->sendmessage('settempbed',$_options['message']);
      break;
      case 'setspeed':
        $eqlogic->sendmessage('setspeed',$_options['message']);
        $eqlogic->checkAndUpdateCmd('saveworkSpeed', $_options['message']);
      break;
      case 'pause':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "RUNNING") {
          $eqlogic->sendmessage('pause',1);
        }
      break;
      case 'start':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "IDLE") {
          if (!isset($_options['message']) || empty($_options['message'])) {
            return;
          }
          $filename = dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . str_replace('/', '_', $_options['message']);
          if (file_exists($filename)) {
            $eqlogic->sendmessage('startprintfile',$filename);
          }
        }
      break;
      case 'sendfile':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "IDLE") {
          if (!isset($_options['message']) || empty($_options['message'])) {
            return;
          }
          $filename = dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . str_replace('/', '_', $_options['message']);
          if (file_exists($filename)) {
            $eqlogic->sendmessage('sendfile',$filename);
          }
        }
      break;
      case 'stop':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() != "IDLE") {
          $eqlogic->sendmessage('stop',1);
        }
      break;
      case 'resume':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "PAUSED") {
          $eqlogic->sendmessage('resume',1);
        }
      break;
      case 'reload':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() != "RUNNING") {
          $eqlogic->sendmessage('reload',1);
        }
      break;
      case 'unload':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() != "RUNNING") {
          $eqlogic->sendmessage('unload',1);
        }
      break;
      case 'setpauseifopen':
        $eqlogic->sendmessage('setpauseifopen',1);
      break;
      case 'unsetpauseifopen':
        $eqlogic->sendmessage('unsetpauseifopen',1);
      break;
      case 'setzoffset':
        $eqlogic->sendmessage('setzoffset',$_options['message']);
      break;
      case 'setlight':
        $eqlogic->sendmessage('setlight',100);
      break;
      case 'setfan':
        $eqlogic->sendmessage('setfan',1);
      break;
      case 'unsetlight':
        $eqlogic->sendmessage('setlight',0);
      break;
      case 'unsetfan':
        $eqlogic->sendmessage('setfan',0);
      break;
      case 'setautoconnect':
        $eqlogic->checkAndUpdateCmd('autoconnect', "1");
      break;
      case 'unsetautoconnect':
        $eqlogic->checkAndUpdateCmd('autoconnect', "0");
      break;
      case 'setautoshutdown':
        $eqlogic->checkAndUpdateCmd('autoshutdown', "1");
      break;
      case 'unsetautoshutdown':
        $eqlogic->checkAndUpdateCmd('autoshutdown', "0");
      break;
      case 'execcomande':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "IDLE") {
          $eqlogic->sendmessage('execcomande', $_options['message']);
        }
      break;
    }
  }
  /*     * **********************Getteur Setteur*************************** */
}