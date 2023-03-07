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

    $this->create_element('sendfile'  ,'sendfile'     ,'action','other');
    $this->create_element('sendgcode' ,'sendgcode'    ,'action','other');

    $this->create_element('reload','reload','action','other');
    $this->create_element('unload','unload','action','other');

    $this->create_element('setpauseifopen'  ,'setpauseifopen'  ,'action','other');
    $this->create_element('unsetpauseifopen','unsetpauseifopen','action','other');
    
    $this->create_element('newtempnozzle','newtempnozzle','action','texte');
    $this->create_element('newtempbed'   ,'newtempbed'   ,'action','texte');
    $this->create_element('newspeed'     ,'newspeed'     ,'action','texte');
    $this->create_element('newlazer'     ,'newlazer'     ,'action','texte');
    $this->create_element('newzoffset'   ,'newzoffset'   ,'action','other');
    $this->create_element('settempnozzle','settempnozzle','action','other');
    $this->create_element('settempbed'   ,'settempbed'   ,'action','other');
    $this->create_element('setspeed'     ,'setspeed'     ,'action','other');
    $this->create_element('setlazer'     ,'setlazer'     ,'action','other');
    $this->create_element('setzoffset'   ,'setzoffset'   ,'action','other');
    $this->create_element('setlight'     ,'setlight'     ,'action','other');
    $this->create_element('setfan'       ,'setfan'       ,'action','other');
    $this->create_element('unsetlight'   ,'unsetlight'   ,'action','other');
    $this->create_element('unsetfan'     ,'unsetfan'     ,'action','other');

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
    $this->create_element('elapsedTime'               ,'elapsedTime'               ,'info','string');
    $this->create_element('remainingTime'             ,'remainingTime'             ,'info','string');
    $this->create_element('enclosure'                 ,'enclosure'                 ,'info','string');
    $this->create_element('rotaryModule'              ,'rotaryModule'              ,'info','string');
    $this->create_element('emergencyStopButton'       ,'emergencyStopButton'       ,'info','string');
    $this->create_element('airPurifier'               ,'airPurifier'               ,'info','string');
    $this->create_element('isEnclosureDoorOpen'       ,'isEnclosureDoorOpen'       ,'info','string');
    $this->create_element('stopIfEnclosureDoorOpen'   ,'stopIfEnclosureDoorOpen'   ,'info','string');
    $this->create_element('EnclosureLight'            ,'EnclosureLight'            ,'info','string');
    $this->create_element('stopIfEnclosureFan'        ,'stopIfEnclosureFan'        ,'info','string');
    $this->create_element('zoffset'                   ,'zoffset'                   ,'info','string');
  }

  // Fonction exécutée automatiquement avant la suppression de l'équipement
  public function preRemove() {
  }

  // Fonction exécutée automatiquement après la suppression de l'équipement
  public function postRemove() {
  }

  private function create_element($newcmd,$newname,$newtype,$newsubtype){
    $newelement = $this->getCmd(null, $newcmd);
    if (!is_object($newelement)) {
      $newelement = new snapmakerCmd();
      $newelement->setName(__($newname, __FILE__));
    }
    $newelement->setEqLogic_id($this->getId());
    $newelement->setLogicalId($newcmd);
    $newelement->setType($newtype);
    $newelement->setSubType($newsubtype);
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

  /*     * **********************Getteur Setteur*************************** */

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
        $eqlogic->checkAndUpdateCmd('status', $info['status']);
        $eqlogic->checkAndUpdateCmd('homed', $info['homed']);
        $eqlogic->checkAndUpdateCmd('toolHead', $info['toolHead']);
        $eqlogic->checkAndUpdateCmd('nozzleTemperature', $info['nozzleTemperature']);
        $eqlogic->checkAndUpdateCmd('nozzleTargetTemperature', $info['nozzleTargetTemperature']);
        $eqlogic->checkAndUpdateCmd('heatedBedTemperature', $info['heatedBedTemperature']);
        $eqlogic->checkAndUpdateCmd('heatedBedTargetTemperature', $info['heatedBedTargetTemperature']);
        $eqlogic->checkAndUpdateCmd('isFilamentOut', $info['isFilamentOut']);
        $eqlogic->checkAndUpdateCmd('workSpeed', $info['workSpeed']);
        $eqlogic->checkAndUpdateCmd('printStatus', $info['printStatus']);
        $eqlogic->checkAndUpdateCmd('fileName', $info['fileName']);
        $eqlogic->checkAndUpdateCmd('totalLines', $info['totalLines']);
        $eqlogic->checkAndUpdateCmd('estimatedTime', $info['estimatedTime']);
        $eqlogic->checkAndUpdateCmd('currentLine', $info['currentLine']);
        $eqlogic->checkAndUpdateCmd('progress', $info['progress']);
        $eqlogic->checkAndUpdateCmd('elapsedTime', $info['elapsedTime']);
        $eqlogic->checkAndUpdateCmd('remainingTime', $info['remainingTime']);
        $eqlogic->checkAndUpdateCmd('toolHead', $info['toolHead']);
        $eqlogic->checkAndUpdateCmd('enclosure', $info['moduleList']['enclosure']);
        $eqlogic->checkAndUpdateCmd('rotaryModule', $info['moduleList']['rotaryModule']);
        $eqlogic->checkAndUpdateCmd('emergencyStopButton', $info['moduleList']['emergencyStopButton']);
        $eqlogic->checkAndUpdateCmd('airPurifier', $info['moduleList']['airPurifier']);
        $eqlogic->checkAndUpdateCmd('isEnclosureDoorOpen', $info['isEnclosureDoorOpen']);
      case 'enclosure':
        $eqlogic->checkAndUpdateCmd('stopIfEnclosureDoorOpen', $info['stopIfEnclosureDoorOpen']);
        $eqlogic->checkAndUpdateCmd('EnclosureLight', $info['EnclosureLight']);
        $eqlogic->checkAndUpdateCmd('stopIfEnclosureDoorOpen', $info['stopIfEnclosureDoorOpen']);
        break;
      case 'file':
        $eqlogic->checkAndUpdateCmd('stopIfEnclosureDoorOpen', $info['stopIfEnclosureDoorOpen']);
        $eqlogic->checkAndUpdateCmd('EnclosureLight', $info['EnclosureLight']);
        $eqlogic->checkAndUpdateCmd('stopIfEnclosureFan', $info['stopIfEnclosureFan']);
        break;
      case 'connect':
        
      break;
      case 'disconnect':
        
      break;
      case 'sendfile':
        //uploadFile
      break;
      case 'startprintfile':
        //uploadGcodeFile
      break;
      case 'settempnozzle':
        
      break;
      case 'settempbed':
        
      break;
      case 'setspeed':
        
      break;
      case 'setlazer':
        
      break;
      case 'pause':
        
      break;
      case 'start':
        
      break;
      case 'stop':
        
      break;
      case 'resume':
        
      break;
      case 'reload':
        
      break;
      case 'unload':
        
      break;
      case 'setpauseifopen':
        
      break;
      case 'unsetpauseifopen':
        
      break;
      case 'setzoffset':
        
      break;
      case 'setlight':
        
      break;
      case 'setfan':
        
      break;
    }   
  }

  /*     * **********************Getteur Setteur*************************** */

}