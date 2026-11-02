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
		<title>Connexion - Les gâteaux de Steph</title>
	</head>
	<body>
		<?php require 'header.php'; ?>

		<h1>Se connecter :</h1>

		<form action="connecter.php" method="post" autocomplete="off">
			<?= champJeton() ?>

			<label for="mail">Adresse e-mail :</label>
			<input type="email" id="mail" name="mail" value="<?= h($_GET['mail'] ?? '') ?>" required>
			<br><br>

			<label for="mdp">Mot de passe :</label>
			<input type="password" id="mdp" name="mdp" required>
			<br><br>

			<input class="valider" type="submit" value="Envoyer">
		</form>
	</body>
</html>