<?php
require_once 'securite.php';
require_once 'bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') rediriger('connexion.php');
verifierJeton();

$mail = trim((string)($_POST['mail'] ?? ''));
$mdp  = (string)($_POST['mdp'] ?? '');
$retour = 'connexion.php?mail=' . urlencode($mail);

if ($mail === '' || $mdp === '') {
    message('Veuillez remplir tous les champs.');
    rediriger($retour);
}

$st = getBD()->prepare('SELECT * FROM clients WHERE mail = ?');
$st->execute([$mail]);
$client = $st->fetch();

if (!$client || !password_verify($mdp, $client['mdp'])) {
    message('Adresse e-mail ou mot de passe incorrect.');
    rediriger($retour);
}

session_regenerate_id(true);   // protège contre la fixation / le vol de session
$_SESSION['client'] = [
    'id'      => $client['id_client'],
    'nom'     => $client['nom'],
    'prenom'  => $client['prenom'],
    'adresse' => $client['adresse'],
    'numero'  => $client['numero'],
    'mail'    => $client['mail'],
];
rediriger('../index.php');