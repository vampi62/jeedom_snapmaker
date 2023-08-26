# Plugin snapmaker


le plugin snapmaker permet de piloter une ou plusieurs imprimantes 3D snapmaker.

l'imprimante snapmaker est un équipement multi outils (imprimante 3D, graveur laser, fraiseuse CNC), le plugin

1) ajouter un équipement snapmaker
pour ce faire creer un équipement par imprimante et renseigner les parametres suivrant :
- ip : l'adresse ip de l'imprimante
- port : port du daemon snapmaker (utiliser un port non utiliser par un autre service de la machine)
- cycle : temps de rafraichissement du daemon (en seconde) (0.3 par defaut)
- token : laisser vide il se remplira de lui meme apres la premiere connexion a l'imprimante

si votre imprimante est sur une prise connecter et que les informations de la prise sont renseigner dans jeedom, entrer les element ci-dessous :
- status alim : retour d'etat de la prise (0 ou 1)
- on alim : action pour allumer la prise
- off alim : action pour eteindre la prise

2) une fois tous les elements rentrer, sauvegarder l'équipement et patienter 1 minute pour que le daemon se lance avant de connecter l'imprimante. (si le daemon ne se lance pas, aller dans les parametres du plugin et cliquer sur le bouton pour relancer le daemon)


3) une fois le daemon lancer, cliquer sur le bouton "connexion" de l'équipement pour lancer la connexion à l'imprimante.

4) si il s'agit de la première connexion, un message vous demandera de valider la connexion sur l'imprimante.

5) si la connexion s'établie, le widget de l'équipement se mettra a jour avec les informations de l'imprimante, et plusieurs section apparaitront.

6) 3 menu son disponible en bas à droite du widget :
- dans le menu par defaut vous trouverez les informations de l'imprimante (etat, temperature, etc...)
- dans le 2eme menu vous pouvez deposer des fichiers gcode et les selectionner pour les envoyer à l'imprimante
- dans le 3eme menu vous pouvez lancer des commandes manuelles, déplacer les axes, etc...

si vous avez des questions ou des remarques, n'hesitez pas ouvrir une issue sur le github du plugin : https://github.com/vampi62/jeedom_snapmaker