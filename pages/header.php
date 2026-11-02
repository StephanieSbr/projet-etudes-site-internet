<header>
	<a href="<?= $racine ?>index.php"><span class="logo">Les gâteaux de Steph</span></a>
	<nav>
		<a class="accueil" href="<?= $racine ?>index.php"><img src="<?= $racine ?>images/home.jpg" alt="Accueil"></a>
		<a class="contact" href="<?= $racine ?>contact/contact.php">Contact</a>

		<?php if (!isset($_SESSION['client'])) { ?>

			<div class="menu-profil">
				<img src="<?= $racine ?>images/profil.jpg" alt="Profil">
				<div class="dropdown">
					<a href="<?= $racine ?>pages/nouveau.php">Nouveau client</a>
					<a href="<?= $racine ?>pages/connexion.php">Se connecter</a>
				</div>
			</div>

			<div class="menu-panier">
				<img src="<?= $racine ?>images/panier.jpg" alt="Panier">
				<div class="dropdown">
					<a href="<?= $racine ?>pages/nouveau.php">Nouveau client</a>
					<a href="<?= $racine ?>pages/connexion.php">Se connecter</a>
				</div>
			</div>

		<?php } else { ?>

			<div class="menu-profil">
				<img src="<?= $racine ?>images/profil.jpg" alt="Profil">
				<div class="dropdown">
					<a href="<?= $racine ?>articles/historique.php">Historique des commandes</a>
					<a href="<?= $racine ?>pages/deconnexion.php">Se déconnecter</a>
				</div>
			</div>

			<a class="panier" href="<?= $racine ?>articles/panier.php"><img src="<?= $racine ?>images/panier.jpg" alt="Panier"></a>

		<?php } ?>
	</nav>
</header>

<?php if (!empty($_SESSION['flash'])) { ?>
	<div class="messages">
		<?php foreach ($_SESSION['flash'] as $m) { ?>
			<p><?= h($m) ?></p>
		<?php } ?>
	</div>
	<?php $_SESSION['flash'] = []; ?>
<?php } ?>