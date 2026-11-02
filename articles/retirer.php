<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') rediriger('panier.php');
verifierJeton();

$idArt = filter_var($_POST['id_art'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($idArt === false) rediriger('panier.php');

$idClient = (int)$_SESSION['client']['id'];
$bdd = initialiserPage();

try {
    transaction($bdd);
    $st = $bdd->prepare('SELECT id_art FROM articles WHERE id_art = ? FOR UPDATE');
    $st->execute([$idArt]);
    $bdd->prepare('DELETE FROM reservations WHERE id_art = ? AND id_client = ?')->execute([$idArt, $idClient]);
    majChrono($bdd, $idArt);
    $bdd->commit();
    message('Article retiré de votre panier.');
} catch (Throwable $e) {
    if ($bdd->inTransaction()) $bdd->rollBack();
    error_log($e->getMessage());
    message('Une erreur est survenue.');
}
rediriger('panier.php');