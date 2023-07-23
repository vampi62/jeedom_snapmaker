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

try {
    require_once dirname(__FILE__) . '/../../../../core/php/core.inc.php';
    include_file('core', 'authentification', 'php');

    if (!isConnect()) {
        throw new Exception(__('401 - Accès non autorisé', __FILE__));
    }

  /* Fonction permettant l'envoi de l'entête 'Content-Type: application/json'
    En V3 : indiquer l'argument 'true' pour contrôler le token d'accès Jeedom
    En V4 : autoriser l'exécution d'une méthode 'action' en GET en indiquant le(s) nom(s) de(s) action(s) dans un tableau en argument
  */
    ajax::init();
    $action = init('action');
    if ($action == 'upload') {
      $uploaddir = __DIR__ . '/../../data/' . init('idelement');
      if (!file_exists($uploaddir)) {
        mkdir($uploaddir);
      }
      if (!file_exists($uploaddir)) {
        throw new Exception(__('Répertoire de téléversement non trouvé :', __FILE__) . ' ' . $uploaddir);
      }
      if (!isset($_FILES['file'])) {
        throw new Exception(__('Aucun fichier trouvé. Vérifiez le paramètre PHP (post size limit)', __FILE__));
      }
      $extension = strtolower(strrchr($_FILES['file']['name'], '.'));
      if (!in_array($extension, array('.gcode'))) {
        throw new Exception(__('Extension du fichier non valide (autorisé .gcode) :', __FILE__) . ' ' . $extension);
      }
      if (filesize($_FILES['file']['tmp_name']) > 500000000) {
        throw new Exception(__('Le fichier est trop gros (maximum 500Mo)', __FILE__));
      }
      if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploaddir . '/' . $_FILES['file']['name'])) {
        throw new Exception(__('Impossible de déplacer le fichier temporaire', __FILE__));
      }
      if (!file_exists($uploaddir . '/' . $_FILES['file']['name'])) {
        throw new Exception(__('Impossible de téléverser le fichier (limite du serveur web ?)', __FILE__));
      }
      ajax::success();
    }
    if ($action == 'remove') {
      
    }
    if ($action == 'download') {
      
    }

    throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . $action);
    /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
