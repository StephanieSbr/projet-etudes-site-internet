<?php
const DEBUG = false;   // mettre true seulement en développement
ini_set('display_errors', DEBUG ? '1' : '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

// En-têtes de protection
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');

// Session durcie (cookie inaccessible au JavaScript, envoyé en HTTPS si dispo)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

// Échappement HTML (protection XSS)
function h($valeur): string {
    return htmlspecialchars((string)$valeur, ENT_QUOTES, 'UTF-8');
}

// Redirection propre
function rediriger(string $url): void {
    header('Location: ' . $url);
    exit();
}

// Messages affichés dans le header
function message(string $texte): void {
    $_SESSION['flash'][] = $texte;
}

// Garde de connexion
function exigerConnexion(string $racine): void {
    if (!isset($_SESSION['client'])) {
        message('Vous devez être connecté.');
        rediriger($racine . 'pages/connexion.php');
    }
}

// CSRF : un jeton aléatoire par session
function jeton(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function champJeton(): string {
    return '<input type="hidden" name="csrf" value="' . h(jeton()) . '">';
}

function verifierJeton(): void {
    $recu = $_POST['csrf'] ?? '';
    if (!is_string($recu) || !hash_equals(jeton(), $recu)) {
        http_response_code(403);
        exit('Requête refusée (jeton invalide).');
    }
}