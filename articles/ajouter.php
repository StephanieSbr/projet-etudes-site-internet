<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') rediriger('../index.php');
verifierJeton();

$idArt = filter_var($_POST['id_art'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$qte   = filter_var($_POST['quantite'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000]]);
if ($idArt === false || $qte === false) {
    message('Données invalides.');
    rediriger('../index.php');
}

$idClient = (int)$_SESSION['client']['id'];
$bdd = initialiserPage();
$retour = 'article.php?id_art=' . $idArt;

try {
    transaction($bdd);

    // Verrou sur l'article : deux clients ne peuvent pas réserver en même temps le même article
    $st = $bdd->prepare('SELECT quantite FROM articles WHERE id_art = ? FOR UPDATE');
    $st->execute([$idArt]);
    $stock = $st->fetchColumn();
    if ($stock === false) {
        $bdd->rollBack();
        message('Article introuvable.');
        rediriger('../index.php');
    }

    // Quantité déjà réservée par les AUTRES clients
    $st = $bdd->prepare('SELECT COALESCE(SUM(quantite), 0) FROM reservations WHERE id_art = ? AND id_client <> ?');
    $st->execute([$idArt, $idClient]);
    $autres = (int)$st->fetchColumn();

    $maxPourMoi = (int)$stock - $autres;
    if ($qte > $maxPourMoi) {
        $bdd->rollBack();
        message('Quantité indisponible : il ne reste que ' . max(0, $maxPourMoi) . ' exemplaire(s) pour vous.');
        rediriger($retour);
    }

    if ($qte === 0) {
        $bdd->prepare('DELETE FROM reservations WHERE id_art = ? AND id_client = ?')->execute([$idArt, $idClient]);
    } else {
        $bdd->prepare(
            'INSERT INTO reservations (id_client, id_art, quantite) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE quantite = VALUES(quantite), date_reservation = NOW()'
        )->execute([$idClient, $idArt, $qte]);
    }

    majChrono($bdd, $idArt);   // démarre ou annule le chrono de l'article
    $bdd->commit();

    message($qte === 0 ? 'Article retiré de votre panier.' : 'Votre panier a été mis à jour.');
    rediriger('panier.php');

} catch (Throwable $e) {
    if ($bdd->inTransaction()) $bdd->rollBack();
    error_log($e->getMessage());
    message('Une erreur est survenue, réessayez.');
    rediriger($retour);
}