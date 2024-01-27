# Plugin snapmaker

le plugin snapmaker permet de piloter une ou plusieurs imprimantes 3D snapmaker.

l'imprimante snapmaker est un équipement multi outils (imprimante 3D, graveur laser, fraiseuse CNC), le plugin

1) ajouter un équipement snapmaker
pour ce faire creer un équipement par imprimante et renseigner les parametres suivrant :
- ip : l'adresse ip de l'imprimante
- port : port du daemon snapmaker (port non utiliser par aucun autre service ou deamon entre 12100 et 12199)
- cycle : temps de rafraichissement du daemon (en seconde) (0.3 par defaut)
- token : laisser vide il se remplira de lui meme apres la premiere connexion a l'imprimante

si vous disposez d'une prise connecter pour allumer et eteindre l'imprimante, vous pouvez renseigner les parametres suivant :
- status alim : retour d'etat de la prise (0 ou 1)
- on alim : action pour allumer la prise
- off alim : action pour eteindre la prise


2) première connexion à l'imprimante
- une fois votre imprimante démarrer, cliquer sur le bouton "connexion" de l'équipement, le plugin va alors tenter de se connecter à l'imprimante et de recuperer le token d'authentification.
- un message apparaitra dans le widget de l'équipement pour vous demander de valider la connexion sur l'ecran de l'imprimante.
- une fois validée, le plugin pourra se connecter à votre imprimante et récupérer son état.
 
3) navigation dans le widget, 3 menu sont disponible en bas à droite du widget :
- dans le menu par defaut vous trouverez les informations de l'imprimante (etat, temperature, module, etc...)
- dans le 2eme menu vous pouvez déposer des fichiers (gcode, nc et cnc) et les selectionner pour les envoyer à l'imprimante
- dans le 3eme menu vous pouvez lancer des commandes manuelles, déplacer les axes, etc... (visible uniquement si l'imprimante est connecter et qu'elle n'a pas de tache en cours)

4) info
- les opérations de calibration et de mise à jour du firmware ne sont pas disponible dans le plugin, il faut les faire depuis l'écran de l'imprimante
- le menu axes s'adaptera pour afficher les commandes adéquat en fonction de l'outil connecter sur l'imprimante (imprimante, graveur laser ou fraiseuse CNC) (!seul le module impression avec 2 buses n'a pas été intégré pour le moment!)
- le laser et la fraiseuse CNC peuvent être activé a 40% de leur puissance maximum (attention au laser, veuillez l'activer que si vous controler l'imprimante à proximité de celle ci, sur une durée courte et avec les protections adéquat)
- une fois que votre imprimante commence un travail si vous avez renseigner les parametres de la prise connecter, vous pouvez activer une option dans le premier menu (sous menu alim) pour éteindre l'imprimante une fois le travail fini jusqu'a 5 minute apres la fin du travail.
- si votre impression vient à être interrompu (coupure de courant, etc...) le plugin vous proposera de reprendre l'impression à partir de la couche ou elle s'est arrêté (attention à ne pas toucher à l'imprimante pour ne pas déplacer un axe ce qui risque de causé un décalage) (fonctionnalité en cours de test uniquement pour les impressions 3D)
    - pour ce faire, il faut que le fichier que vous avez imprimer soit toujours présent dans la liste des fichiers de l'imprimante et que votre imprimante ne soit pas connecter à jeedom
    - selectionner le fichier que vous avez imprimer dans la liste des fichiers
    - cliquer sur le bouton "reprise" dans le menu des fichiers
    - le plugin va alors créer un nouveau fichier avec le nom "reprise_" + le nom de votre fichier
    - vous pouvez alors connecter votre imprimante à jeedom et lancer l'impression du fichier "reprise_" + le nom de votre fichier
    - l'imprimante va alors se positionner à la couche ou elle s'est arrêté et reprendre l'impression (verifier qu'il n'y a pas de décalage durant la reprise)

- lancer une impression ne fonctionne pas si le capteur de filament ne detecte pas de filament, (si vous rencontrer des problèmes avec ce capteur, vous pouvez le désactiver avec une commande "M412 S0" et le réactiver avec "M412 S1" (attention si vous le désactiver, prévoyer assez de filament pour votre impression))

ce plugin a été testé sur une snapmaker 2.0 A350T avec les modules impression 3D, graveur laser 1.6W et 20W, fraiseuse CNC et caisson de protection.
les modèles similaire devrait fonctionner mais faite un retour sur le forum si vous avez des soucis, ou des suggestions d'amélioration.

si vous avez des questions ou des remarques, n'hesitez pas ouvrir une issue sur le github du plugin : https://github.com/vampi62/jeedom_snapmaker
ou directement sur le forum jeedom community : https://community.jeedom.com/