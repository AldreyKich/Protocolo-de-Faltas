<?php
/**
 * Configurações gerais da aplicação
 */

// URL base — ajuste conforme seu ambiente
if (!defined('BASE_URL')) {
    define('BASE_URL', '/protocolo_faltas/');
}

// Diretório de uploads
if (!defined('UPLOAD_DIR')) {
    define('UPLOAD_DIR', __DIR__ . '/../uploads/');
}
if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', BASE_URL . 'uploads/');
}

// Tipos de arquivo permitidos no upload
if (!defined('ALLOWED_TYPES')) {
    define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'application/pdf']);
}
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
}

// Fuso horário
date_default_timezone_set('America/Sao_Paulo');

if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
}

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/db.php';

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' .
        htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') .
        '">';
}

function csrf_is_valid(?string $token): bool {
    return is_string($token)
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $token);
}

function cpf_is_valid(string $cpf): bool {
    $cpf = preg_replace('/\D/', '', $cpf);

    if (strlen($cpf) !== 11 || preg_match('/^(\d)\1{10}$/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        $sum = 0;
        for ($i = 0; $i < $t; $i++) {
            $sum += (int) $cpf[$i] * (($t + 1) - $i);
        }

        $digit = ((10 * $sum) % 11) % 10;
        if ((int) $cpf[$t] !== $digit) {
            return false;
        }
    }

    return true;
}
