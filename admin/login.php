<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';

// Já logado → redireciona
if (!empty($_SESSION['usuario_id'])) {
    header('Location: dashboard.php');
    exit;
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if ($email === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare('SELECT * FROM usuario WHERE email = ? AND ativo = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($senha, $user['senha'])) {
            $_SESSION['usuario_id']    = $user['id_usuario'];
            $_SESSION['usuario_nome']  = $user['nome'];
            $_SESSION['usuario_perfil']= $user['perfil'];
            session_regenerate_id(true);
            header('Location: dashboard.php');
            exit;
        } else {
            $erro = 'E-mail ou senha inválidos.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Protocolo Eletrônico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="login-wrapper">
    <div class="login-card card shadow-lg">
        <div class="card-header-primary text-center py-4">
            <i class="bi bi-file-earmark-check-fill fs-1 mb-2 d-block"></i>
            <h4 class="mb-0 fw-bold">Protocolo Eletrônico</h4>
            <small class="opacity-75">Área Administrativa</small>
        </div>
        <div class="card-body p-4">
            <?php if ($erro): ?>
                <div class="alert alert-danger alert-auto d-flex align-items-center gap-2">
                    <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label" for="email">
                        <i class="bi bi-envelope"></i> E-mail
                    </label>
                    <input type="email" id="email" name="email" class="form-control"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                           placeholder="seu@email.com" required autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="senha">
                        <i class="bi bi-lock"></i> Senha
                    </label>
                    <div class="input-group">
                        <input type="password" id="senha" name="senha" class="form-control"
                               placeholder="••••••••" required>
                        <button type="button" class="btn btn-outline-secondary"
                                onclick="toggleSenha()" tabindex="-1">
                            <i class="bi bi-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="<?= BASE_URL ?>public/index.php" class="text-muted small">
                    <i class="bi bi-arrow-left"></i> Voltar ao portal público
                </a>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
<script>
function toggleSenha() {
    const s = document.getElementById('senha');
    const i = document.getElementById('eyeIcon');
    if (s.type === 'password') {
        s.type = 'text';
        i.className = 'bi bi-eye-slash';
    } else {
        s.type = 'password';
        i.className = 'bi bi-eye';
    }
}
</script>
</body>
</html>
