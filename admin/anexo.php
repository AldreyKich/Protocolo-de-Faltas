<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    http_response_code(404);
    exit('Arquivo não encontrado.');
}

$db = getDB();
$stmt = $db->prepare("
    SELECT a.nome_arquivo, a.caminho_arquivo, a.tipo_arquivo
    FROM anexo a
    INNER JOIN protocolo p ON p.id_protocolo = a.id_protocolo
    WHERE a.id_anexo = ?
");
$stmt->execute([$id]);
$anexo = $stmt->fetch();

if (!$anexo) {
    http_response_code(404);
    exit('Arquivo não encontrado.');
}

$nomeArquivo = basename($anexo['caminho_arquivo']);
$caminho = realpath(UPLOAD_DIR . $nomeArquivo);
$uploadDir = realpath(UPLOAD_DIR);

if (!$caminho || !$uploadDir || strpos($caminho, $uploadDir . DIRECTORY_SEPARATOR) !== 0 || !is_file($caminho)) {
    http_response_code(404);
    exit('Arquivo não encontrado.');
}

$tipo = $anexo['tipo_arquivo'] ?: 'application/octet-stream';
$nomeDownload = basename($anexo['nome_arquivo']);

header('X-Content-Type-Options: nosniff');
header('Content-Type: ' . $tipo);
header('Content-Length: ' . filesize($caminho));
header('Content-Disposition: inline; filename="' . str_replace('"', '', $nomeDownload) . '"');
readfile($caminho);
exit;
