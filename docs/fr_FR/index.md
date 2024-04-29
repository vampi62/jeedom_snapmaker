# Plugin Snapmaker

Le plugin Snapmaker permet de piloter une ou plusieurs imprimantes 3D Snapmaker.

L'imprimante Snapmaker est un équipement multi-outils (imprimante 3D, graveur laser, fraiseuse CNC).
Le plugin permet de :
- stoker les fichiers gcode, nc et cnc sur jeedom
- recuperer les informations de l'imprimante
- controler manuellement les axes de l'imprimante
- lancer une impression/gravure
- envoyer des commandes gcode
- reprendre une impression interrompue (en cours de test)


- (si lier a une prise connecter : programmer l'arret de l'alimentation de l'imprimante une fois son travail terminer)
## 1) Ajouter un équipement Snapmaker

Pour ce faire, créez un équipement par imprimante et renseignez les paramètres suivants :
- **IP** : l'adresse IP de l'imprimante
- **Port** : port du daemon Snapmaker (port non utilisé par aucun autre service ou démon entre 12100 et 12199)
- **Cycle** : temps de rafraîchissement du daemon (en secondes) (0.3 par défaut)
- **Token** : laisser vide, il se remplira de lui-même après la première connexion à l'imprimante

Si vous disposez d'une prise connectée pour allumer et éteindre l'imprimante, vous pouvez renseigner les paramètres suivants :
- **Status alim** : jeedom info retour d'état de la prise (0 ou 1)
- **On alim** : jeedom action pour allumer la prise
- **Off alim** : jeedom action pour éteindre la prise

## 2) Première connexion à l'imprimante

- Une fois votre imprimante démarrée, cliquez sur le bouton "Connexion" de l'équipement. Le plugin va alors tenter de se connecter à l'imprimante et de récupérer le token d'authentification.
- Un message apparaîtra dans le widget de l'équipement pour vous demander de valider la connexion sur l'écran de l'imprimante.
- Une fois validée, le plugin pourra se connecter à votre imprimante et récupérer son état.

## 3) Navigation dans le widget

3 menus sont disponibles en bas à droite du widget :
- Dans le menu par défaut, vous trouverez les informations de l'imprimante (état, température, module, etc.).
- Dans le 2ème menu, vous pouvez déposer des fichiers (gcode, nc et cnc) et les sélectionner pour les envoyer à l'imprimante.
- Dans le 3ème menu, vous pouvez lancer des commandes manuelles, déplacer les axes, etc. (visible uniquement si l'imprimante est connectée et qu'elle n'a pas de tâche en cours).

## 4) Informations

- Les opérations de calibration et de mise à jour du firmware ne sont pas disponibles dans le plugin, il faut les faire depuis l'écran de l'imprimante.
- Le menu axes s'adaptera pour afficher les commandes adéquates en fonction de l'outil connecté sur l'imprimante (imprimante, graveur laser ou fraiseuse CNC). Seul le module impression avec 2 buses n'a pas été intégré pour le moment.
- Le laser et la fraiseuse CNC peuvent être activés à 40% de leur puissance maximum (attention au laser, veuillez l'activer que si vous contrôlez l'imprimante à proximité de celle-ci, sur une durée courte et avec les protections adéquates).
- Une fois que votre imprimante commence un travail, si vous avez renseigné les paramètres de la prise connectée, vous pouvez activer une option dans le premier menu (sous-menu alim) pour éteindre l'imprimante une fois le travail fini jusqu'à 5 minutes après la fin du travail.
- Si votre impression vient à être interrompue (coupure de courant, etc.), le plugin vous proposera de reprendre l'impression à partir de la couche où elle s'est arrêtée (attention à ne pas toucher à l'imprimante pour ne pas déplacer un axe ce qui risque de causer un décalage) (fonctionnalité en cours de test uniquement pour les impressions 3D).
    - Pour ce faire, il faut que le fichier que vous avez imprimé soit toujours présent dans la liste des fichiers de l'imprimante et que votre imprimante ne soit pas connectée à Jeedom.
    - Sélectionnez le fichier que vous avez imprimé dans la liste des fichiers.
    - Cliquez sur le bouton "Reprise" dans le menu des fichiers.
    - Le plugin va alors créer un nouveau fichier avec le nom "reprise_" + le nom de votre fichier.
    - Vous pouvez alors connecter votre imprimante à Jeedom et lancer l'impression du fichier "reprise_" + le nom de votre fichier.
    - L'imprimante va alors se positionner à la couche où elle s'est arrêtée et reprendre l'impression (vérifiez qu'il n'y a pas de décalage durant la reprise).

- Lancer une impression ne fonctionne pas si le capteur de filament ne détecte pas de filament (si vous rencontrez des problèmes avec ce capteur, vous pouvez le désactiver avec une commande "M412 S0" et le réactiver avec "M412 S1" (attention si vous le désactivez, prévoyez assez de filament pour votre impression)).

Ce plugin a été testé sur une Snapmaker 2.0 A350T avec les modules impression 3D, graveur laser 1.6W et 20W, fraiseuse CNC et caisson de protection. Les modèles similaires devraient fonctionner, mais faites un retour sur le forum si vous avez des soucis ou des suggestions d'amélioration.

Si vous avez des questions ou des remarques, n'hésitez pas à ouvrir une issue sur le GitHub du plugin : https://github.com/vampi62/jeedom_snapmaker ou directement sur le forum Jeedom Community : https://community.jeedom.com/