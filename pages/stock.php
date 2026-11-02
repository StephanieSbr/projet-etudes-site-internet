<?php
require_once __DIR__ . '/bd.php';

const DUREE_CHRONO_MIN = 5;

// Transaction : READ COMMITTED pour toujours lire les dernières données validées
function transaction(PDO $bdd): void {
    $bdd->exec('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
    $bdd->beginTransaction();
}

/*
 * Démarre ou annule le chrono d'un article.
 * À appeler DANS une transaction, après avoir verrouillé la ligne de l'article.
 * Chrono = tout le stock est dans les paniers (disponible <= 0 et stock > 0).
 */
function majChrono(PDO $bdd, int $idArt): void {
    $st = $bdd->prepare(
        'SELECT a.quantite, a.fin_chrono,
                a.quantite - COALESCE((SELECT SUM(r.quantite) FROM reservations r WHERE r.id_art = a.id_art), 0) AS dispo
         FROM articles a WHERE a.id_art = ?'
    );
    $st->execute([$idArt]);
    $a = $st->fetch();
    if (!$a) return;

    if ($a['quantite'] > 0 && $a['dispo'] <= 0) {
        if ($a['fin_chrono'] === null) {   // une seule date de départ, partagée par tous
            $bdd->prepare('UPDATE articles SET fin_chrono = DATE_ADD(NOW(), INTERVAL ' . DUREE_CHRONO_MIN . ' MINUTE) WHERE id_art = ?')
                ->execute([$idArt]);
        }
    } elseif ($a['fin_chrono'] !== null) {  // du stock redevient disponible : plus de chrono
        $bdd->prepare('UPDATE articles SET fin_chrono = NULL WHERE id_art = ?')->execute([$idArt]);
    }
}

/*
 * Libère les articles dont le chrono est dépassé :
 * supprime les réservations, prévient les clients, remet le stock à la vente.
 */
function nettoyerReservations(PDO $bdd): void {
    $ids = $bdd->query('SELECT id_art FROM articles WHERE fin_chrono IS NOT NULL AND fin_chrono <= NOW()')
               ->fetchAll(PDO::FETCH_COLUMN);

    foreach ($ids as $idArt) {
        try {
            transaction($bdd);

            // on reverrouille et on revérifie (un autre processus a pu passer avant)
            $st = $bdd->prepare('SELECT nom FROM articles WHERE id_art = ? AND fin_chrono IS NOT NULL AND fin_chrono <= NOW() FOR UPDATE');
            $st->execute([$idArt]);
            $nom = $st->fetchColumn();
            if ($nom === false) { $bdd->rollBack(); continue; }

            $st = $bdd->prepare('SELECT id_client FROM reservations WHERE id_art = ?');
            $st->execute([$idArt]);
            $texte = 'Délai de 5 minutes dépassé : vos exemplaires de "' . $nom . '" ont été retirés de votre panier.';
            $ins = $bdd->prepare('INSERT INTO notifications (id_client, texte) VALUES (?, ?)');
            foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $idClient) {
                $ins->execute([$idClient, $texte]);
            }

            $bdd->prepare('DELETE FROM reservations WHERE id_art = ?')->execute([$idArt]);
            $bdd->prepare('UPDATE articles SET fin_chrono = NULL WHERE id_art = ?')->execute([$idArt]);
            $bdd->commit();
        } catch (Throwable $e) {
            if ($bdd->inTransaction()) $bdd->rollBack();
            error_log($e->getMessage());
        }
    }
}

// À appeler en haut de chaque page : nettoyage + récupération des messages du client
function initialiserPage(): PDO {
    $bdd = getBD();
    nettoyerReservations($bdd);

    if (isset($_SESSION['client'])) {
        $idClient = (int)$_SESSION['client']['id'];
        $st = $bdd->prepare('SELECT texte FROM notifications WHERE id_client = ?');
        $st->execute([$idClient]);
        foreach ($st->fetchAll(PDO::FETCH_COLUMN) as $texte) {
            message($texte);
        }
        $bdd->prepare('DELETE FROM notifications WHERE id_client = ?')->execute([$idClient]);
    }
    return $bdd;
}

// Lignes du panier d'un client, avec secondes restantes du chrono de chaque article
function lignesPanier(PDO $bdd, int $idClient): array {
    $st = $bdd->prepare(
        'SELECT r.id_art, r.quantite, a.nom, a.prix,
                GREATEST(TIMESTAMPDIFF(SECOND, NOW(), a.fin_chrono), 0) AS restant
         FROM reservations r JOIN articles a ON a.id_art = r.id_art
         WHERE r.id_client = ? ORDER BY a.nom'
    );
    $st->execute([$idClient]);
    return $st->fetchAll();
}

// Affichage du chrono (le calcul vient du serveur, le JavaScript ne fait qu'afficher)
function chronoHtml($secondes): string {
    if ($secondes === null) return '';
    return '<span class="chrono" data-restant="' . (int)$secondes . '">--:--</span>';
}

function scriptChrono(): string {
    return <<<'JS'
<script>
var chronos = document.getElementsByClassName("chrono");
var fins = [];
var rechargement = false;
for (var i = 0; i < chronos.length; i++) {
    fins.push(Date.now() + parseInt(chronos[i].getAttribute("data-restant")) * 1000);
}
function afficherChronos() {
    for (var i = 0; i < chronos.length; i++) {
        var reste = Math.max(0, Math.round((fins[i] - Date.now()) / 1000));
        var s = reste % 60;
        chronos[i].innerHTML = Math.floor(reste / 60) + ":" + (s < 10 ? "0" : "") + s;
        if (reste === 0 && !rechargement) {
            rechargement = true;
            setTimeout(function () { location.reload(); }, 1500);  // le serveur décide de l'expiration
        }
    }
}
if (chronos.length > 0) { afficherChronos(); setInterval(afficherChronos, 1000); }
</script>
JS;
}