<?php
require_once 'pages/securite.php';
require_once 'pages/stock.php';
$racine = '';
$bdd = initialiserPage();

$articles = $bdd->query(
    'SELECT a.*, a.quantite - COALESCE((SELECT SUM(r.quantite) FROM reservations r WHERE r.id_art = a.id_art), 0) AS dispo
     FROM articles a ORDER BY a.id_art'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="styles/style.css" type="text/css">
		<title>Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require 'pages/header.php'; ?>

		<h1>Les meilleurs gâteaux au meilleur prix !</h1>

		<?php if (isset($_SESSION['client'])) { ?>
			<div>Bonjour <?= h($_SESSION['client']['prenom'] . ' ' . $_SESSION['client']['nom']) ?></div>
		<?php } ?>

		<div class="articles">
			<?php foreach ($articles as $ligne) { ?>
				<div class="article" id="article_<?= (int)$ligne['id_art'] ?>">
					<a href="articles/article.php?id_art=<?= (int)$ligne['id_art'] ?>">
						<img class="im_prod" src="<?= h($ligne['img']) ?>" alt="<?= h($ligne['nom']) ?>">
						<span class="nom"><?= h($ligne['nom']) ?></span>
						<span class="prix"><?= number_format($ligne['prix'], 2) ?> €</span>
						<?php if ($ligne['dispo'] > 0) { ?>
							<span class="stock">Restant : <?= (int)$ligne['dispo'] ?></span>
						<?php } elseif ($ligne['quantite'] > 0) { ?>
							<span class="stock epuise">Réservé</span>
						<?php } else { ?>
							<span class="stock epuise">Épuisé</span>
						<?php } ?>
					</a>
				</div>
			<?php } ?>
		</div>
	</body>
</html>