<?php
require_once '../pages/securite.php';
require_once '../pages/stock.php';
$racine = '../';
$bdd = initialiserPage();

$idArt = filter_var($_GET['id_art'] ?? null, FILTER_VALIDATE_INT);
if ($idArt === false || $idArt < 1) rediriger('../index.php');

$idClient = isset($_SESSION['client']) ? (int)$_SESSION['client']['id'] : 0;

$st = $bdd->prepare(
    'SELECT a.*,
            COALESCE((SELECT SUM(r.quantite) FROM reservations r WHERE r.id_art = a.id_art), 0) AS reserve,
            COALESCE((SELECT r.quantite FROM reservations r WHERE r.id_art = a.id_art AND r.id_client = ?), 0) AS mien,
            GREATEST(TIMESTAMPDIFF(SECOND, NOW(), a.fin_chrono), 0) AS restant
     FROM articles a WHERE a.id_art = ?'
);
$st->execute([$idClient, $idArt]);
$ligne = $st->fetch();
if (!$ligne) {
    message('Article introuvable.');
    rediriger('../index.php');
}

$dispo = $ligne['quantite'] - $ligne['reserve'];
$maxPourMoi = $dispo + $ligne['mien'];   // ce que je peux avoir au maximum dans mon panier
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title><?= h($ligne['nom']) ?> - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require '../pages/header.php'; ?>

		<h1><?= h($ligne['nom']) ?></h1>

		<table>
			<tr>
				<td><img src="../<?= h($ligne['img']) ?>" alt="<?= h($ligne['nom']) ?>"></td>
				<td class="compo">
					<p>Composition :</p>
					<ul>
						<?php foreach (explode(',', $ligne['composition']) as $ingredient) { ?>
							<li><?= h(trim($ingredient)) ?></li>
						<?php } ?>
					</ul>
				</td>
			</tr>
		</table>

		<p class="stock-info">
			<?php if ($dispo > 0) { ?>
				Restant en stock : <strong><?= (int)$dispo ?></strong>
			<?php } elseif ($ligne['quantite'] > 0) { ?>
				<span class="epuise">Tout le stock est actuellement réservé.</span>
			<?php } else { ?>
				<span class="epuise">Épuisé</span>
			<?php } ?>

			<?php if ($ligne['mien'] > 0) { ?>
				<br>Dans votre panier : <strong><?= (int)$ligne['mien'] ?></strong>
			<?php } ?>

			<?php if ($ligne['restant'] !== null) { ?>
				<br>Fin de la réservation dans <?= chronoHtml($ligne['restant']) ?>
			<?php } ?>
		</p>

		<?php if (isset($_SESSION['client']) && $maxPourMoi > 0) { ?>
			<form action="ajouter.php" method="post" autocomplete="off">
				<?= champJeton() ?>
				<input type="hidden" name="id_art" value="<?= (int)$ligne['id_art'] ?>">

				<label for="quantite">Nombre d'exemplaires :</label>
				<input type="number" id="quantite" name="quantite"
				       value="<?= $ligne['mien'] > 0 ? (int)$ligne['mien'] : 1 ?>"
				       min="<?= $ligne['mien'] > 0 ? 0 : 1 ?>" max="<?= (int)$maxPourMoi ?>" required>
				<br><br>
				<input class="valider" type="submit" value="<?= $ligne['mien'] > 0 ? 'Modifier' : 'Ajouter' ?>">
			</form>
		<?php } elseif (!isset($_SESSION['client'])) { ?>
			<p><a href="../pages/connexion.php">Connectez-vous</a> pour ajouter cet article à votre panier.</p>
		<?php } ?>

		<?= scriptChrono() ?>
	</body>
</html>