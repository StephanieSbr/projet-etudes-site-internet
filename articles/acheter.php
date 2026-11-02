<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');
$racine = '../';

// Page de confirmation (GET, évite de renvoyer le formulaire au rafraîchissement)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (($_GET['ok'] ?? '') !== '1') rediriger('panier.php');
    ?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title>Commande enregistrée - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require '../pages/header.php'; ?>
		<h1>Votre commande a bien été enregistrée.</h1>
		<p>Merci <?= h($_SESSION['client']['prenom']) ?>.</p>
		<div class="actions">
			<a class="bouton bouton-secondaire" href="historique.php">Historique des commandes</a>
			<a class="bouton" href="../index.php">Retour</a>
		</div>
	</body>
</html>
    <?php
    exit();
}

// Traitement (POST)
verifierJeton();
$idClient = (int)$_SESSION['client']['id'];
$bdd = initialiserPage();

try {
    transaction($bdd);

    // Articles du panier, toujours verrouillés dans le même ordre (évite les blocages croisés)
    $st = $bdd->prepare('SELECT id_art FROM reservations WHERE id_client = ? ORDER BY id_art');
    $st->execute([$idClient]);
    $articles = $st->fetchAll(PDO::FETCH_COLUMN);

    if (count($articles) === 0) {
        $bdd->rollBack();
        message('Votre réservation a expiré ou votre panier est vide.');
        rediriger('panier.php');
    }

    foreach ($articles as $idArt) {
        // 1. verrou de l'article
        $st = $bdd->prepare('SELECT id_art FROM articles WHERE id_art = ? FOR UPDATE');
        $st->execute([$idArt]);

        // 2. ma réservation existe-t-elle toujours ?
        $st = $bdd->prepare('SELECT quantite FROM reservations WHERE id_client = ? AND id_art = ? FOR UPDATE');
        $st->execute([$idClient, $idArt]);
        $qte = $st->fetchColumn();
        if ($qte === false) throw new RuntimeException('expire');

        // 3. décrément du stock réel : impossible de passer sous zéro
        $st = $bdd->prepare('UPDATE articles SET quantite = quantite - ? WHERE id_art = ? AND quantite >= ?');
        $st->execute([$qte, $idArt, $qte]);
        if ($st->rowCount() !== 1) throw new RuntimeException('stock');

        // 4. commande, puis libération de la réservation
        $bdd->prepare('INSERT INTO commandes (id_art, id_client, quantite, envoi) VALUES (?, ?, ?, 0)')
            ->execute([$idArt, $idClient, $qte]);
        $bdd->prepare('DELETE FROM reservations WHERE id_client = ? AND id_art = ?')->execute([$idClient, $idArt]);

        majChrono($bdd, (int)$idArt);
    }

    $bdd->commit();
    rediriger('acheter.php?ok=1');

} catch (Throwable $e) {
    if ($bdd->inTransaction()) $bdd->rollBack();
    if ($e instanceof RuntimeException) {
        message('Votre réservation a expiré : la commande n\'a pas été enregistrée.');
    } else {
        error_log($e->getMessage());
        message('Une erreur est survenue, la commande n\'a pas été enregistrée.');
    }
    rediriger('panier.php');
}