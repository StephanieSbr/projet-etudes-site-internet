# Les Gâteaux de Steph

## Présentation

Les Gâteaux de Steph est un site de vente en ligne consacré aux pâtisseries. Il propose un catalogue de produits avec leurs prix et disponibilités, ainsi qu'un parcours client permettant de créer un compte, de se connecter, de gérer un panier et de suivre ses commandes. Les disponibilités sont liées aux quantités en stock et aux réservations enregistrées.

Le projet est développé en PHP et s'appuie sur une base de données MySQL. Il est conçu pour fonctionner localement avec MAMP sur macOS ou WampServer sur Windows.

## Prérequis

- MAMP ou WampServer avec les services Apache, PHP et MySQL démarrés.
- L'extension PHP `pdo_mysql` activée.
- Un navigateur web.

## Installation locale

1. Placez le dossier du projet dans la racine web de votre environnement :
	- MAMP : `/Applications/MAMP/htdocs/`
	- WampServer : `C:\\wamp64\\www\\` (ou `C:\\wamp\\www\\` selon l'installation).
2. Démarrez Apache et MySQL depuis MAMP ou WampServer.
3. Ouvrez phpMyAdmin depuis la page d'accueil de MAMP/WampServer.
4. Créez une base de données nommée `Les_gateaux_de_Steph`, puis importez le fichier `les_gateaux_de_steph.sql` dans cette base.
5. Vérifiez la connexion à la base dans `pages/bd.php`. Les paramètres actuellement utilisés sont `127.0.0.1`, le port MySQL `8889`, la base `Les_gateaux_de_Steph`, l'utilisateur `root` et le mot de passe `root`. Adaptez le port et les identifiants à votre configuration locale ; WampServer utilise souvent le port `3306` et un mot de passe vide par défaut.
6. Ouvrez le site dans votre navigateur :
	- MAMP avec son port Apache par défaut : <http://localhost:8888/projet-etudes-site-internet/>
	- WampServer avec Apache sur le port `80` : <http://localhost/projet-etudes-site-internet/>


## Structure du projet

- `index.php` : catalogue et page d'accueil.
- `articles/` : fiches produits, panier, réservations et commandes.
- `pages/` : connexion à la base, comptes clients et éléments communs du site.
- `styles/` : feuille de style.
- `images/` : images utilisées par le site.
- `les_gateaux_de_steph.sql` : structure et données initiales de la base.