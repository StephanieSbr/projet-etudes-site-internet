<?php
require_once 'securite.php';
$racine = '../';
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="../styles/style.css" type="text/css">
		<title>Nouveau client - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require 'header.php'; ?>

		<h1>Nouveau client :</h1>

		<form action="enregistrement.php" method="post" autocomplete="off">
			<?= champJeton() ?>

			<label for="n">Nom :</label><br>
			<input type="text" id="n" name="n" maxlength="100" value="<?= h($_GET['n'] ?? '') ?>" required>
			<br><br>

			<label for="p">Prénom :</label><br>
			<input type="text" id="p" name="p" maxlength="100" value="<?= h($_GET['p'] ?? '') ?>" required>
			<br><br>

			<label for="adr">Adresse :</label><br>
			<input type="text" id="adr" name="adr" value="<?= h($_GET['adr'] ?? '') ?>" required>
			<br><br>

			<label for="num">Numéro de téléphone :</label><br>
			<input type="tel" id="num" name="num" maxlength="20" value="<?= h($_GET['num'] ?? '') ?>" required>
			<br><br>

			<label for="mail">Adresse e-mail :</label><br>
			<input type="email" id="mail" name="mail" maxlength="100" value="<?= h($_GET['mail'] ?? '') ?>" required>
			<br><br>

			<label for="mdp1">Mot de passe (4 caractères minimum) :</label><br>
			<input type="password" id="mdp1" name="mdp1" minlength="4" required>
			<br><br>

			<label for="mdp2">Confirmer votre mot de passe :</label><br>
			<input type="password" id="mdp2" name="mdp2" minlength="4" required>
			<br><br>

			<input class="valider" type="submit" value="Envoyer">
		</form>
	</body>
</html>