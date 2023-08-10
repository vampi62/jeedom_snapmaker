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
  public static function cron() {}
  */

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
  public function preInsert() {
  }

  // Fonction exécutée automatiquement après la création de l'équipement
  public function postInsert() {
  }

  // Fonction exécutée automatiquement avant la mise à jour de l'équipement
  public function preUpdate() {
  }

  // Fonction exécutée automatiquement après la mise à jour de l'équipement
  public function postUpdate() {
  }

  // Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
  public function preSave() {
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
    $this->create_element('stopIfEnclosureDoorOpen'   ,'stopIfEnclosureDoorOpen'   ,'info','string');
    $this->create_element('EnclosureLight'            ,'EnclosureLight'            ,'info','string');
    $this->create_element('EnclosureFan'              ,'EnclosureFan'              ,'info','string');
    $this->create_element('zoffset'                   ,'zoffset'                   ,'info','string');
    $path = dirname(__FILE__) . '/../../data/' . $this->getId();
    if (!file_exists($path)) {
      mkdir($path, 0777, true);
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('statusalim')));
    if (!is_object($cmd)) {
      $this->setConfiguration('statusalim', '');
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('onalim')));
    if (!is_object($cmd)) {
      $this->setConfiguration('onalim', '');
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('offalim')));
    if (!is_object($cmd)) {
      $this->setConfiguration('offalim', '');
    }
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
    $path = dirname(__FILE__) . '/../../data/' . $this->getId();
    if (file_exists($path)) {
      rmdir($path);
    }
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
  public function sendmessage($message,$value){
    log::add('snapmaker','debug','sendmessage : '.$message . ' : ' . $value);
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
    }
    else {
      $replace['#onalim_id#'] = "-1";
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('offalim')));
    if (is_object($cmd)) {
      $replace['#offalim_id#'] = $cmd->getId();
    }
    else {
      $replace['#offalim_id#'] = "-1";
    }
    $cmd = cmd::byId(str_replace("#","",$this->getConfiguration('statusalim')));
    if (is_object($cmd)) {
      $replace['#aliminfo#'] = $cmd->execCmd();
      $replace['#aliminfo_id#'] = $cmd->getId();
      $replace['#aliminfo_valueDate#'] = date('d-m-Y H:i:s',strtotime($cmd->getValueDate()));
      $replace['#aliminfo_collectDate#'] = date('d-m-Y H:i:s',strtotime($cmd->getCollectDate()));
      $replace['#aliminfo_updatetime#'] = date('d-m-Y H:i:s',strtotime( $this->getConfiguration('updatetime')));
    }
    else {
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
          }
          fclose($filecont);
          $filelist[] = $file . '-:-' . convert(filesize($filedir)) . '-:-' . date("Y-m-d H:i:s", filemtime($filedir)) . '-:-' . $file_total_lines . '-:-' . $estimated_time . '-:-' . $thumbnail;
        }
        $filelist = implode("-!-", $filelist);
        $eqlogic->checkAndUpdateCmd('filelist', $filelist);
        
      break;
      case 'status':
        $eqlogic->sendmessage('status',1);
        $this->getallvaluearray($info);
      break;
      case 'enclosure':
        $eqlogic->sendmessage('enclosure',1);
        $this->getallvaluearray($info);
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
      case 'settempbed':
        $eqlogic->sendmessage('settempbed',$_options['message']);
      break;
      case 'setspeed':
        $eqlogic->sendmessage('setspeed',$_options['message']);
      break;
      case 'pause':
        $eqlogic->sendmessage('pause',1);
      break;
      case 'start':
        if (!isset($_options['message']) || empty($_options['message'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name)) {
          $eqlogic->sendmessage('startprintfile',$_options['message']);
        }
      break;
      case 'stop':
        $eqlogic->sendmessage('stop',1);
      break;
      case 'resume':
        $eqlogic->sendmessage('resume',1);
      break;
      case 'reload':
        $eqlogic->sendmessage('reload',1);
      break;
      case 'unload':
        $eqlogic->sendmessage('unload',1);
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
        $eqlogic->sendmessage('setlight',1);
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
        $eqlogic->sendmessage('execcomande', $_options['message']);
      break;
    }
  }
  private function getallvaluearray($liste, $keyorigin = "") {
    $value_iniore = array("x","y","z","status"); // liste des valeurs a ne pas mettre a jour , x,y,z sont des valuer qui change regulierement et status n'est pas utilise donc pour eviter des ecriture inutile on ne le met pas a jour
    $eqlogic = $this->getEqLogic();
    foreach ($liste as $key => $value) {
      if (is_array($value)) {
        $this->getallvaluearray($value,$keyorigin . "/" .$key);
      }
      else {
        if (in_array($key, $value_iniore)) {
          continue;
        }
        $element = $this->getCmd(null, $keyorigin . $key);
        if (is_object($element)) {
          $eqlogic->checkAndUpdateCmd($keyorigin . $key, $value);
        }
        else {
          log::add('snapmaker','debug',$keyorigin . $key . " - n'existe pas pour l'eqlogic " . $eqlogic->getName());
        }
      }
    }
  }
  /*     * **********************Getteur Setteur*************************** */

}