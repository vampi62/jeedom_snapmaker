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
    if (!in_array($extension, array('.gcode','.nc','.cnc'))) {
      throw new Exception(__('Extension du fichier non valide (autorisé .gcode, .nc, .cnc) :', __FILE__) . ' ' . $extension);
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
  if (init('action') == 'reprise') {
    $uploaddir = __DIR__ . '/../../data/' . init('id');
    if (!file_exists($uploaddir)) {
      throw new \Exception(__('Impossible de trouver le répertoire de l\'équipement', __FILE__).init('id'));
    }
    $file = $uploaddir . '/' . init('file');
    if (!file_exists($file)) {
      throw new \Exception(__('Impossible de trouver le fichier', __FILE__).init('file'));
    }
    # si le fichier n'est pas un .gcode
    if (strtolower(strrchr($file, '.')) != '.gcode') {
      throw new \Exception(__('Le fichier n\'est pas un .gcode', __FILE__).init('file'));
    }
    $numeroLigne = intval(init('line'));
    $pourcentage = 1-(intval(init('percent')) / 100);
    $contenuFichierSource = file($file);
    $fileis = init('fileis');
    $valeur_de_Z = -1;
    $valeur_de_E0 = -1;
    $valeur_de_E1 = -1;
    $valeur_de_EX = -1;
    $valeur_de_TR = -1;
    $valeur_de_TX = -1;
    for ($i = $numeroLigne-1; $i >= 0; $i--) {
      if ((strpos($contenuFichierSource[$i], 'Z') !== false) && ($valeur_de_Z == -1)) {
        $valeur_de_Z = substr($contenuFichierSource[$i], strpos($contenuFichierSource[$i], 'Z') + 1);
      }
      if ((strpos($contenuFichierSource[$i], 'E') !== false) && ($valeur_de_EX == -1)) {
        $valeur_de_EX = substr($contenuFichierSource[$i], strpos($contenuFichierSource[$i], 'E') + 1);
      }
      if ($valeur_de_Z != -1) {
        if ($fileis == "dualExtruderToolheadForSM2") {
          if (strpos($contenuFichierSource[$i], ' T') !== false) {
            $valeur_de_TX = inval(substr($contenuFichierSource[$i], strpos($contenuFichierSource[$i], ' T') + 1));
            if ($valeur_de_T == -1) {
              $valeur_de_TR = $valeur_de_TX;
            }
            if ($valeur_de_TX == 0) {
              $valeur_de_E0 = $valeur_de_EX;
            } else {
              $valeur_de_E1 = $valeur_de_EX;
            }
            $valeur_de_EX = -1;
          }
          if (($valeur_de_E0 != -1) && ($valeur_de_E1 != -1) && ($valeur_de_TR != -1)) {
            break;
          }
        } else {
          if ($valeur_de_EX != -1) {
            $valeur_de_E0 = $valeur_de_EX;
            break;
          }
        }
      }
    }
    if (($fileis == "singleExtruderToolheadForSM2") || ($fileis == "dualExtruderToolheadForSM2")) {// 3d print avec 1 ou 2 extrudeurs
      $LignesActuel = -1;
      $EstimeActuel = -1;
      $temp1 = -1;
      $temp2 = -1;
      $bedtemp = -1;
      $contenuPremieresLignes = array_slice($contenuFichierSource, 0, 40);
      for ($i = 0; $i <= count($contenuPremieresLignes); $i++) {
        if (strpos($contenuPremieresLignes[$i], ';file_total_lines') !== false) {
          $LignesActuel = intval(substr($contenuPremieresLignes[$i], strpos($contenuPremieresLignes[$i], ': ') + 1));
          $contenuPremieresLignes[$i] = str_replace($LignesActuel, strval($LignesActuel - $numeroLigne), $contenuPremieresLignes[$i]);
        }
        else if (strpos($contenuPremieresLignes[$i], ';estimated_time') !== false) {
          $EstimeActuel = intval(substr($contenuPremieresLignes[$i], strpos($contenuPremieresLignes[$i], ': ') + 1));
          $contenuPremieresLignes[$i] = str_replace($EstimeActuel, strval($EstimeActuel * $pourcentage), $contenuPremieresLignes[$i]);
        }
        else if (strpos($contenuPremieresLignes[$i], ';nozzle_temperature') !== false) {
          $temp1 = intval(substr($contenuPremieresLignes[$i], strpos($contenuPremieresLignes[$i], ': ') + 1));
        }
        else if (strpos($contenuPremieresLignes[$i], ';nozzle_1_temperature') !== false) {
          $temp2 = intval(substr($contenuPremieresLignes[$i], strpos($contenuPremieresLignes[$i], ': ') + 1));
        }
        else if (strpos($contenuPremieresLignes[$i], ';build_plate_temperature') !== false) {
          $bedtemp = intval(substr($contenuPremieresLignes[$i], strpos($contenuPremieresLignes[$i], ': ') + 1));
        }
      }
    }
    if ($temp1 != -1) {
      $contenuPremieresLignes[] = "T0\n";
      $contenuPremieresLignes[] = "M104 S" . strval($temp1) ."\n";
      $contenuPremieresLignes[] = "G92 E" . $valeur_de_E0 ."\n";
    }
    if (($temp2 != -1) && ($fileis == "dualExtruderToolheadForSM2")) {
      $contenuPremieresLignes[] = "T1\n";
      $contenuPremieresLignes[] = "M104 S" . strval($temp2) ."\n";
      $contenuPremieresLignes[] = "G92 E" . $valeur_de_E1 ."\n";
    }
    if ($bedtemp != -1) {
      $contenuPremieresLignes[] = "M140 S" . strval($bedtemp) ."\n";
    }
    if ($fileis == "dualExtruderToolheadForSM2") {
      $contenuPremieresLignes[] = "T" . $valeur_de_TR ."\n";
    }
    $contenuPremieresLignes[] = "G1 Z" . $valeur_de_Z ."\n";
    $contenuCopie = array_slice($contenuFichierSource, $numeroLigne-1);
    $contenuCopie = array_merge($contenuPremieresLignes, $contenuCopie);
    $newfile = $uploaddir . '/reprise_' . init('file');
    file_put_contents($newfile, implode("", $contenuCopie));
    ajax::success();
  }
  throw new Exception(__('Aucune méthode correspondante à', __FILE__) . ' : ' . init('action'));
  /*     * *********Catch exeption*************** */
}
catch (Exception $e) {
    ajax::error(displayException($e), $e->getCode());
}
