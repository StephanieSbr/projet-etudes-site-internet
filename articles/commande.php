<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');
$racine = '../';
$bdd = initialiserPage();
$lignes = lignesPanier($bdd, (int)$_SESSION['client']['id']);

if (count($lignes) === 0) {
    message('Votre panier est vide.');
    rediriger('panier.php');
}
$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title>Commande - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require '../pages/header.php'; ?>

		<h1>Commande</h1>
		<div>Récapitulatif de votre commande :</div>

		<table class="tableau-panier">
			<tr><th>Article</th><th>Quantité</th><th>Temps restant</th></tr>
			<?php foreach ($lignes as $l) { $total += $l['prix'] * $l['quantite']; ?>
				<tr>
					<td><?= h($l['nom']) ?></td>
					<td><?= (int)$l['quantite'] ?></td>
					<td><?= $l['restant'] !== null ? chronoHtml($l['restant']) : '-' ?></td>
				</tr>
			<?php } ?>
		</table>

		<p class="total">Montant de votre commande : <?= number_format($total, 2) ?> €</p>

		<p>La commande sera expédiée à l'adresse suivante :<br>
			<?= h($_SESSION['client']['nom']) ?> <?= h($_SESSION['client']['prenom']) ?><br>
			<?= h($_SESSION['client']['adresse']) ?>
		</p>

		<div class="actions">
			<a class="bouton bouton-secondaire" href="panier.php">Retour au panier</a>
			<form class="inline" action="acheter.php" method="post">
				<?= champJeton() ?>
				<button type="submit" class="bouton">Valider</button>
			</form>
		</div>

		<?= scriptChrono() ?>
	</body>
</html>