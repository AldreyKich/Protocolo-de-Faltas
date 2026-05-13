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
    define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'application/pdf']);
}
if (!defined('MAX_FILE_SIZE')) {
    define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5 MB
}

// Fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db.php';
