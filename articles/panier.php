<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');
$racine = '../';
$bdd = initialiserPage();
$lignes = lignesPanier($bdd, (int)$_SESSION['client']['id']);
$total = 0;
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title>Mon panier - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require '../pages/header.php'; ?>

		<h1>Mon panier</h1>

		<?php if (count($lignes) === 0) { ?>

			<p>Votre panier ne contient aucun article.</p>
			<div class="actions"><a class="bouton" href="../index.php">Voir les gâteaux</a></div>

		<?php } else { ?>

			<table class="tableau-panier">
				<tr>
					<th>Article</th>
					<th>Prix unitaire</th>
					<th>Quantité</th>
					<th>Prix</th>
					<th>Temps restant</th>
					<th>Actions</th>
				</tr>
				<?php foreach ($lignes as $l) {
					$prixLigne = $l['prix'] * $l['quantite'];
					$total += $prixLigne;
				?>
					<tr>
						<td><?= h($l['nom']) ?></td>
						<td><?= number_format($l['prix'], 2) ?> €</td>
						<td><?= (int)$l['quantite'] ?></td>
						<td><?= number_format($prixLigne, 2) ?> €</td>
						<td><?= $l['restant'] !== null ? chronoHtml($l['restant']) : '-' ?></td>
						<td>
							<a class="bouton bouton-petit" href="article.php?id_art=<?= (int)$l['id_art'] ?>">Modifier</a>
							<form class="inline" action="retirer.php" method="post">
								<?= champJeton() ?>
								<input type="hidden" name="id_art" value="<?= (int)$l['id_art'] ?>">
								<button type="submit" class="bouton bouton-petit bouton-danger">Retirer</button>
							</form>
						</td>
					</tr>
				<?php } ?>
			</table>

			<p class="total">Total : <?= number_format($total, 2) ?> €</p>

			<div class="actions">
				<a class="bouton bouton-secondaire" href="../index.php">Continuer mes achats</a>
				<a class="bouton" href="commande.php">Passer la commande</a>
			</div>

		<?php } ?>

		<?= scriptChrono() ?>
	</body>
</html>