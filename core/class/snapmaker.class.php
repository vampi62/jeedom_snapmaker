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
        if ($snapmaker->getCmd(null, 'status')->execCmd() == "0") {
          $cmd = $snapmaker->getCmd(null, 'connect');
          if (!is_object($cmd)) {
            continue;
          }
          $cmd->execCmd();
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
  */
  public static function cron10() {
    foreach (self::byType('snapmaker', true) as $snapmaker) { //parcours tous les équipements actifs du plugin
      $cmd = $snapmaker->getCmd(null, 'refresh');
      if (!is_object($cmd)) {
        continue;
      }
      $cmd->execCmd();
    }
  }

  /*
  * Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
  public static function cron15() {}
  */

  /*
  * Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
  public static function cron30() {}
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
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
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

  // Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
  public function postSave() {
    $this->create_element('refresh','Rafraichir','action','other');
    $this->create_element('pause'  ,'pause'     ,'action','other');
    $this->create_element('start'  ,'start'     ,'action','other');
    $this->create_element('stop'   ,'stop'      ,'action','other');
    $this->create_element('resume' ,'resume'    ,'action','other');

    $this->create_element('connect'   ,'connect'   ,'action','other');
    $this->create_element('disconnect','disconnect','action','other');

    $this->create_element('setautoconnect'  ,'setautoconnect'  ,'action','other');
    $this->create_element('unsetautoconnect','unsetautoconnect','action','other');

    $this->create_element('sendgcode'  ,'sendgcode'  ,'action','other');
    $this->create_element('execcomande','execcomande','action','message');
    $this->create_element('sendfile'   ,'sendfile'   ,'action','other');

    $this->create_element('reload','reload','action','other');
    $this->create_element('unload','unload','action','other');

    $this->create_element('setpauseifopen'  ,'setpauseifopen'  ,'action','other');
    $this->create_element('unsetpauseifopen','unsetpauseifopen','action','other');
    
    $this->create_element('settempnozzle','settempnozzle','action','other');
    $this->create_element('settempbed'   ,'settempbed'   ,'action','other');
    $this->create_element('setspeed'     ,'setspeed'     ,'action','other');
    $this->create_element('setzoffset'   ,'setzoffset'   ,'action','other');
    $this->create_element('setlight'     ,'setlight'     ,'action','other');
    $this->create_element('setfan'       ,'setfan'       ,'action','other');
    $this->create_element('unsetlight'   ,'unsetlight'   ,'action','other');
    $this->create_element('unsetfan'     ,'unsetfan'     ,'action','other');
    $this->create_element('saveworkSpeed','saveworkSpeed','info'  ,'string');
    
    $this->create_element('autoconnect'               ,'autoconnect'               ,'info','string');
    $this->create_element('filelist'                  ,'filelist'                  ,'info','string');
    $this->create_element('status'                    ,'status'                    ,'info','string');
    $this->create_element('homed'                     ,'homed'                     ,'info','string');
    $this->create_element('toolHead'                  ,'toolHead'                  ,'info','string');
    $this->create_element('nozzleTemperature'         ,'nozzleTemperature'         ,'info','string');
    $this->create_element('nozzleTargetTemperature'   ,'nozzleTargetTemperature'   ,'info','string');
    $this->create_element('heatedBedTemperature'      ,'heatedBedTemperature'      ,'info','string');
    $this->create_element('heatedBedTargetTemperature','heatedBedTargetTemperature','info','string');
    $this->create_element('isFilamentOut'             ,'isFilamentOut'             ,'info','string');
    $this->create_element('workSpeed'                 ,'workSpeed'                 ,'info','string');
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
    $this->sendmessage("updateip", $this->getConfiguration("adresseip", "none"));
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
    $newelement->setTemplate('dashboard',$newtemplate);
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
		return $this->postToHtml($version, template_replace($replace, $widgetType));
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
          // lire les 100 premire ligne a la recherche d'un des éléments suivant : ;thumbnail, ;file_total_lines, ;estimated_time. Si trouvé, on récupère le texte de la ligne et on le stocke dans une variable
          $thumbnail = '';
          $file_total_lines = '';
          $estimated_time = '';
          $header_type = '';
          $tool_head = '';
          $is_rotate = '';
          for ($i = 0; $i < 100; $i++) {
            $line = fgets($filecont);
            if (strpos($line, ';thumbnail:') !== false) {
              $thumbnail = substr($line, strpos($line, ';thumbnail:') + strlen(';thumbnail:') + 1);
              $thumbnail = str_replace(array("\r", "" . PHP_EOL), '', $thumbnail);
            }
            if (strpos($line, ';file_total_lines:') !== false) {
              $file_total_lines = substr($line, strpos($line, ';file_total_lines:') + strlen(';file_total_lines:') + 1);
              $file_total_lines = str_replace(array("\r", "" . PHP_EOL), '', $file_total_lines);
            }
            if (strpos($line, ';estimated_time(s):') !== false) {
              $estimated_time = substr($line, strpos($line, ';estimated_time(s):') + strlen(';estimated_time(s):') + 1);
              $estimated_time = str_replace(array("\r", "" . PHP_EOL), '', $estimated_time);
            }
            if (strpos($line, ';header_type:') !== false) {
              $header_type = substr($line, strpos($line, ';header_type:') + strlen(';header_type:') + 1);
              $header_type = str_replace(array("\r", "" . PHP_EOL), '', $header_type);
            }
            if (strpos($line, ';tool_head:') !== false) {
              $tool_head = substr($line, strpos($line, ';tool_head:') + strlen(';tool_head:') + 1);
              $tool_head = str_replace(array("\r", "" . PHP_EOL), '', $tool_head);
            }
            if (strpos($line, ';is_rotate:') !== false) {
              $is_rotate = substr($line, strpos($line, ';is_rotate:') + strlen(';is_rotate:') + 1);
              $is_rotate = str_replace(array("\r", "" . PHP_EOL), '', $is_rotate);
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
        $eqlogic->checkAndUpdateCmd('saveworkSpeed', '100');
      break;
      case 'disconnect':
        $eqlogic->sendmessage('disconnect',1);
      break;
      case 'settempnozzle':
        $eqlogic->sendmessage('settempnozzle',$_options['message']);
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
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "RUNNING") {
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
      case 'execcomande':
        if ($eqlogic->getCmd(null, "printStatus")->execCmd() == "IDLE") {
          $eqlogic->sendmessage('execcomande', $_options['message']);
        }
      break;
    }
  }
  /*     * **********************Getteur Setteur*************************** */
}