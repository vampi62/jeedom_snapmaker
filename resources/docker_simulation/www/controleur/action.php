<?php
/*
	Ce fichier PHP effectue telle ou telle action selon le contenu des gets envoyés
*/
if(isset($_GET['action']))
{
	switch(strtolower($_GET['action']))	{
		// on utilise ici un switch pour inclure telle ou telle page selon l'action.
	
		//connection doit être appelé à chaque action securisée car computercraft n'enregistre pas de session
		
		// action libre

		case 'listntp': // compte libre
			include('controleur/listntp.php');
		break;

		case 'listconfig': // compte libre
			include('controleur/listConfig.php');
		break;

		// gestion utilisateur

		case 'inscription': // compte libre
			// paramètres - mdp - pseudo - mdpconfirm - email
			include('controleur/joueur/inscription.php');
		break;
	}
}
?>