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
  */
  public static function cron5() {
    foreach (self::byType('snapmaker', true) as $snapmaker) { //parcours tous les équipements actifs du plugin
      $cmd = $snapmaker->getCmd(null, 'getlistfile');
      if (!is_object($cmd)) {
        continue;
      }
      $cmd->execCmd();

      $returalim = round(jeedom::evaluateExpression($snapmaker->getConfiguration('statusalim')), 1);
      $buttonon = cmd::humanReadableToCmd($snapmaker->getConfiguration('onalim'));
      log::add('snapmaker','debug',$snapmaker->getConfiguration('statusalim'));
      log::add('snapmaker','debug',$returalim);
      log::add('snapmaker','debug',$snapmaker->getConfiguration('onalim'));
      log::add('snapmaker','debug',$buttonon);
      //round(jeedom::evaluateExpression($snapmaker->getConfiguration('offalim')), 1);
    }
  }

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
    $this->create_element('getlistfile','getlistfile','action','other');

    $this->create_element('connect'   ,'connect'   ,'action','other');
    $this->create_element('disconnect','disconnect','action','other');
    $this->create_element('renamefile','renamefile','action','other');
    $this->create_element('deletefile','deletefile','action','other');
    $this->create_element('addfile'   ,'addfile'   ,'action','other');
    $this->create_element('newnamefile','newnamefile','action','texte');
    $this->create_element('newfile'    ,'newfile'    ,'action','texte');

    $this->create_element('setautoconnect'  ,'setautoconnect'  ,'action','other');
    $this->create_element('unsetautoconnect','unsetautoconnect','action','other');

    $this->create_element('sendfile'  ,'sendfile'     ,'action','other');
    $this->create_element('sendgcode' ,'sendgcode'    ,'action','other');

    $this->create_element('reload','reload','action','other');
    $this->create_element('unload','unload','action','other');

    $this->create_element('setpauseifopen'  ,'setpauseifopen'  ,'action','other');
    $this->create_element('unsetpauseifopen','unsetpauseifopen','action','other');
    
    $this->create_element('newtempnozzle','newtempnozzle','action','texte');
    $this->create_element('newtempbed'   ,'newtempbed'   ,'action','texte');
    $this->create_element('newspeed'     ,'newspeed'     ,'action','texte');
    $this->create_element('newzoffset'   ,'newzoffset'   ,'action','texte');
    $this->create_element('settempnozzle','settempnozzle','action','other');
    $this->create_element('settempbed'   ,'settempbed'   ,'action','other');
    $this->create_element('setspeed'     ,'setspeed'     ,'action','other');
    $this->create_element('setzoffset'   ,'setzoffset'   ,'action','other');
    $this->create_element('setlight'     ,'setlight'     ,'action','other');
    $this->create_element('setfan'       ,'setfan'       ,'action','other');
    $this->create_element('unsetlight'   ,'unsetlight'   ,'action','other');
    $this->create_element('unsetfan'     ,'unsetfan'     ,'action','other');

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
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
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
    $replace['#heightfilelist#'] = strval(intval($replace['#height#'])-150);
    $replace['#widthfilelist#'] = strval(intval($replace['#width#']));
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
        $eqlogic->sendmessage('refresh',1);
        $this->getallvaluearray($info);
      break;
      case 'enclosure':
        $eqlogic->sendmessage('enclosure',1);
        $this->getallvaluearray($info);
      break;
      case 'getlistfile':
        $filelist = array();
        $files = array_diff(scandir(dirname(__FILE__) . '/../../data/' . $eqlogic->getId()), array('.', '..'));
        foreach ($files as $file) {
          $filelist[] = $file . '-:-' . filesize(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $file) . '-:-' . date("Y-m-d H:i:s", filemtime(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $file));
        }
        $filelist = implode("-!-", $filelist);
        $eqlogic->checkAndUpdateCmd('filelist', $filelist);
      break;
      case 'addfile':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        if (!isset($_options['fichier']) || !empty($_options['fichier'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode')) {
          $i = 1;
          while (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '_' . $i . '.gcode')) {
            $i++;
          }
          $name = $name . '_' . $i;
        }
        file_put_contents(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode', $_options['fichier']);
      break;
      case 'delfile':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode')) {
          unlink(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode');
        }
      break;
      case 'renamefile':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        if (!isset($_options['newmessage']) || !empty($_options['newmessage'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        $newname = str_replace('/', '_', $_options['newmessage']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode')) {
          rename(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode', dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $newname . '.gcode');
        }
      break;
      case 'connect':
        $eqlogic->sendmessage('connect',1);
      break;
      case 'disconnect':
        $eqlogic->sendmessage('disconnect',1);
      break;
      case 'sendfile':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        if (!isset($_options['fichier']) || !empty($_options['fichier'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode')) {
          $i = 1;
          while (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '_' . $i . '.gcode')) {
            $i++;
          }
          $name = $name . '_' . $i;
        }
        file_put_contents(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode', $_options['fichier']);
        $eqlogic->sendmessage('sendfile',$name);
        unlink(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode');
      break;
      case 'settempnozzle':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        $eqlogic->sendmessage('settempnozzle',$_options['message']);
      break;
      case 'settempbed':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        $eqlogic->sendmessage('settempbed',$_options['message']);
      break;
      case 'setspeed':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        $eqlogic->sendmessage('setspeed',$_options['message']);
      break;
      case 'pause':
        $eqlogic->sendmessage('pause',1);
      break;
      case 'start':
        if (!isset($_options['message']) || !empty($_options['message'])) {
          return;
        }
        $name = str_replace('/', '_', $_options['message']);
        if (file_exists(dirname(__FILE__) . '/../../data/' . $eqlogic->getId() . '/' . $name . '.gcode')) {
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
        $eqlogic->sendmessage('setautoconnect',1);
      break;
      case 'unsetautoconnect':
        $eqlogic->sendmessage('setautoconnect',0);
      break;
    }
  }
  private function getallvaluearray($liste, $keyorigin = "") {
    $eqlogic = $this->getEqLogic();
    foreach ($liste as $key => $value) {
      if (is_array($value)) {
        $this->getallvaluearray($value,$key);
      }
      else {
        $element = $this->getCmd(null, $keyorigin . $key);
        if (is_object($element)) {
          $eqlogic->checkAndUpdateCmd($keyorigin . $key, $value);
        }
        else {
          log::add('snapmaker','debug',$keyorigin . $key . " - n'existe pas pour l'eqligic " . $eqlogic->getName());
        }
      }
    }
    return $result;
  }
  /*     * **********************Getteur Setteur*************************** */

}