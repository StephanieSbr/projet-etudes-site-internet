<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
exigerConnexion('../');
$racine = '../';
$bdd = initialiserPage();

$st = $bdd->prepare(
    'SELECT c.id_commande, c.id_art, a.nom, a.prix, c.quantite, c.envoi
     FROM commandes c JOIN articles a ON a.id_art = c.id_art
     WHERE c.id_client = ? ORDER BY c.id_commande'
);
$st->execute([(int)$_SESSION['client']['id']]);
$commandes = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title>Historique - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require '../pages/header.php'; ?>

		<h1>Historique de mes commandes</h1>

		<?php if (count($commandes) === 0) { ?>
			<p>Vous n'avez passé aucune commande.</p>
		<?php } else { ?>
			<table class="tableau-panier">
				<tr>
					<th>N° commande</th><th>N° article</th><th>Article</th>
					<th>Prix unitaire</th><th>Quantité</th><th>État</th>
				</tr>
				<?php foreach ($commandes as $c) { ?>
					<tr>
						<td><?= (int)$c['id_commande'] ?></td>
						<td><?= (int)$c['id_art'] ?></td>
						<td><?= h($c['nom']) ?></td>
						<td><?= number_format($c['prix'], 2) ?> €</td>
						<td><?= (int)$c['quantite'] ?></td>
						<td><?= $c['envoi'] ? 'Envoyée' : 'En attente d\'envoi' ?></td>
					</tr>
				<?php } ?>
			</table>
		<?php } ?>

		<div class="actions"><a class="bouton" href="../index.php">Retour</a></div>
	</body>
</html>