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
	
	ajax::init(array('upload', 'download'));
	
	if (!isConnect()) {
		throw new Exception(__('401 - Accès non autorisé', __FILE__));
	}
  if (init('action') == 'upload') {
    $uploaddir = __DIR__ . '/../../data/' . init('id');
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
  if (init('action') == 'delete') {
    $uploaddir = __DIR__ . '/../../data/' . init('id');
    if (!file_exists($uploaddir)) {
      throw new \Exception(__('Impossible de trouver le répertoire de l\'équipement', __FILE__).init('id'));
    }
    $file = $uploaddir . '/' . init('file');
    if (!file_exists($file)) {
      throw new \Exception(__('Impossible de trouver le fichier', __FILE__).init('file'));
    }
    if (!unlink($file)) {
      throw new \Exception(__('Impossible de supprimer le fichier', __FILE__).init('file'));
    }
    ajax::success();
  }
  if (init('action') == 'download') {
    $uploaddir = __DIR__ . '/../../data/' . init('id');
    if (!file_exists($uploaddir)) {
      throw new \Exception(__('Impossible de trouver le répertoire de l\'équipement', __FILE__).init('id'));
    }
    $file = $uploaddir . '/' . init('file');
    if (!file_exists($file)) {
      throw new \Exception(__('Impossible de trouver le fichier', __FILE__).init('file'));
    }

    /* 
    system('cd ' . dirname($uploaddir) . ';cp ' . init('file') . ' ' . jeedom::getTmpFolder('downloads') . '/file.gcode');
    $pathfile = jeedom::getTmpFolder('downloads') . '/file.gcode';
    $path_parts = pathinfo($pathfile);
    header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $path_parts['basename']);
    readfile($pathfile);
    if (file_exists(jeedom::getTmpFolder('downloads') . '/file.gcode')) {
      unlink(jeedom::getTmpFolder('downloads') . '/file.gcode');
    }
    ajax::success();
    */
    ajax::success(readfile($file));
  }

  throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
  /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
