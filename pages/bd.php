<?php
function getBD(): PDO {
    static $bdd = null;   // une seule connexion par requête
    if ($bdd === null) {
        $bdd = new PDO(
            'mysql:host=127.0.0.1;port=8889;dbname=Les_gateaux_de_Steph;charset=utf8',
            'root', 'root',
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
    }
    return $bdd;
}