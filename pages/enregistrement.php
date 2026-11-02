<?php
require_once 'securite.php';
require_once 'bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') rediriger('nouveau.php');
verifierJeton();

function enregistrer($nom, $prenom, $adresse, $mail, $numero, $mdp): void {
    $hash = password_hash($mdp, PASSWORD_DEFAULT);
    getBD()->prepare('INSERT INTO clients (nom, prenom, adresse, numero, mail, mdp) VALUES (?, ?, ?, ?, ?, ?)')
           ->execute([$nom, $prenom, $adresse, $numero, $mail, $hash]);
}

$n    = trim((string)($_POST['n'] ?? ''));
$p    = trim((string)($_POST['p'] ?? ''));
$adr  = trim((string)($_POST['adr'] ?? ''));
$num  = trim((string)($_POST['num'] ?? ''));
$mail = trim((string)($_POST['mail'] ?? ''));
$mdp1 = (string)($_POST['mdp1'] ?? '');
$mdp2 = (string)($_POST['mdp2'] ?? '');

// Les mots de passe ne sont jamais renvoyés dans l'URL
$retour = 'nouveau.php?' . http_build_query(['n' => $n, 'p' => $p, 'adr' => $adr, 'num' => $num, 'mail' => $mail]);

if ($n === '' || $p === '' || $adr === '' || $num === '' || $mail === '' || $mdp1 === '' || $mdp2 === '') {
    message('Tous les champs sont obligatoires.');
    rediriger($retour);
}
if (mb_strlen($n) > 100 || mb_strlen($p) > 100 || mb_strlen($mail) > 100) {
    message('Nom, prénom ou e-mail trop long.');
    rediriger($retour);
}
if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
    message('Adresse e-mail invalide.');
    rediriger($retour);
}
if (!preg_match('/^[0-9 +().-]{6,20}$/', $num)) {
    message('Numéro de téléphone invalide.');
    rediriger($retour);
}
if (strlen($mdp1) < 4) {
    message('Le mot de passe doit contenir au moins 4 caractères.');
    rediriger($retour);
}
if ($mdp1 !== $mdp2) {
    message('Les deux mots de passe sont différents.');
    rediriger($retour);
}

try {
    enregistrer($n, $p, $adr, $mail, $num, $mdp1);
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {   // violation de l'unicité du mail
        message('Cette adresse e-mail est déjà utilisée.');
    } else {
        error_log($e->getMessage());
        message('Une erreur est survenue.');
    }
    rediriger($retour);
}

message('Compte créé, vous pouvez vous connecter.');
rediriger('connexion.php?mail=' . urlencode($mail));