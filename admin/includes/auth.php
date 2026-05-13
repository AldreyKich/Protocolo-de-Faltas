<?php
/**
 * Verifica se o usuário está autenticado.
 * Redireciona para login caso não esteja.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'admin/login.php');
    exit;
}

/**
 * Verifica se o usuário possui o perfil exigido.
 * @param string|array $perfis
 */
function requirePerfil($perfis): void {
    if (!is_array($perfis)) $perfis = [$perfis];
    if (!in_array($_SESSION['usuario_perfil'] ?? '', $perfis, true)) {
        http_response_code(403);
        die('<div style="text-align:center;padding:4rem;font-family:sans-serif;">
              <h2>Acesso negado</h2>
              <p>Você não tem permissão para acessar esta página.</p>
              <a href="dashboard.php">Voltar ao painel</a>
             </div>');
    }
}
