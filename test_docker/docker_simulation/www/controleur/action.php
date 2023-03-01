<?php
/*
	Ce fichier PHP effectue telle ou telle action selon le contenu des gets envoyés
*/
if(isset($url[0]))
{
	switch(strtolower($url[0]))	{
		// on utilise ici un switch pour inclure telle ou telle page selon l'action.
	
		//connection doit être appelé à chaque action securisée car computercraft n'enregistre pas de session
		
		// action libre

		case 'connect': // compte libre
			include('controleur/connect.php');
		break;

		case 'connect': // compte libre
			include('controleur/connect.php');
		break;

		case 'status': // compte libre
			include('controleur/status.php');
		break;
	}
}
?>