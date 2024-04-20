# Changelog plugin snapmaker

>**IMPORTANT**
>
>S'il n'y a pas d'information sur la mise à jour, c'est que celle-ci concerne uniquement de la mise à jour de documentation, de traduction ou de texte.

# 20/04/2024
suppression de la majotité des commande jquery pour les remplacer par des commandes javascript

# 19/01/2024

- type generique appliquer sur les commande pour les app mobile
- les commandes de type action nécessitant un paramètre sont maintenant de type message (start, sendfile, settemp, setspeed, setzoffset) pour les scénarios
    - pour les commandes start et sendfile envoyer le nom du fichier que vous voulez envoyer
    - pour les commandes settemp, setspeed et setzoffset envoyer la valeur que vous voulez appliquer (pas de °C,mm, juste le chiffre)

# 06/01/2024

- ajout des commande pour le module d'impresion 3D avec 2 extrudeur
- ajout de la commande pour les modules de gravure laser (tester avec le module 1600mW), le module 20W n'est pas bien reconnu par l'api de snapmaker
- suppression du cron 30
- image par defaut pour les fichiers et le widget n'est plus bloquant avec les fichier venant d'autre slicer que luban

# 06/09/2023

- notification de retour de commande
- affichage d'un message le temps de l'upload d'un fichier vers l'imprimante

# 30/08/2023

- bouton pour creer un fichier reprise si l'impression a ete interrompu (dans la liste des fichiers, sélectionner celui que vous imprimez) (! ne pas faire de home ou deplacer l'impression du plateau)

# 26/08/2023

- si vous avez configurer la partie alimentation de votre machine, vous pouvez programmer l'arret de l'imprimante une fois son travail terminer.
- configurer le "workorigin" pour le module cnc et laser
- auto selection du port utiliser par le demon pour chaque snapmaker

# 23/08/2023

- charger des fichiers nc et cnc dans le plugin
- ajout d'information sur les fichiers selectionner (temps de travail, si module rotatif, type machine, type d'outil)
- blocage des boutons si l'imprimante n'est pas connecter
- blocage du bouton start si le fichier choisi n'est pas compatible avec la machine (type outil, type machine, module rotatif)

# 20/08/2023

- publication du plugin