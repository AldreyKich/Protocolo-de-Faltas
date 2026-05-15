<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';
requirePerfil('ADMINISTRADOR');

$pageTitle = 'Usuários';
$db  = getDB();
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $msg = 'Sessão expirada. Recarregue a página e tente novamente.';
        $msgType = 'danger';
        $_POST['acao'] = '';
    }

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'salvar') {
        $id     = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT) ?: null;
        $nome   = trim($_POST['nome'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $perfil = $_POST['perfil'] ?? '';
        $ativo  = isset($_POST['ativo']) ? 1 : 0;
        $senha  = $_POST['senha'] ?? '';
        $perfisValidos = ['ADMINISTRADOR', 'SECRETARIA', 'VISUALIZADOR'];

        if ($nome === '' || $email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !in_array($perfil, $perfisValidos, true) || ($senha !== '' && strlen($senha) < 8)) {
            $msg = 'Preencha todos os campos obrigatórios.';
            $msgType = 'danger';
        } else {
            try {
                if ($id) {
                    if ($senha !== '') {
                        $hash = password_hash($senha, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("UPDATE usuario SET nome=?, email=?, perfil=?, ativo=?, senha=? WHERE id_usuario=?");
                        $stmt->execute([$nome, $email, $perfil, $ativo, $hash, $id]);
                    } else {
                        $stmt = $db->prepare("UPDATE usuario SET nome=?, email=?, perfil=?, ativo=? WHERE id_usuario=?");
                        $stmt->execute([$nome, $email, $perfil, $ativo, $id]);
                    }
                    $msg = 'Usuário atualizado!';
                } else {
                    if ($senha === '') {
                        $msg = 'Informe a senha para novo usuário.';
                        $msgType = 'danger';
                    } else {
                        $hash = password_hash($senha, PASSWORD_DEFAULT);
                        $stmt = $db->prepare("INSERT INTO usuario (nome, email, senha, perfil, ativo) VALUES (?,?,?,?,?)");
                        $stmt->execute([$nome, $email, $hash, $perfil, $ativo]);
                        $msg = 'Usuário cadastrado!';
                    }
                }
            } catch (PDOException $e) {
                $msg = 'Erro: e-mail já cadastrado.';
                $msgType = 'danger';
            }
        }
    }

    if ($acao === 'excluir') {
        $id = filter_input(INPUT_POST, 'id_usuario', FILTER_VALIDATE_INT);
        if ($id && $id !== (int)$_SESSION['usuario_id']) {
            $db->prepare("DELETE FROM usuario WHERE id_usuario = ?")->execute([$id]);
            $msg = 'Usuário excluído.';
        } else {
            $msg = 'Não é possível excluir o próprio usuário.';
            $msgType = 'danger';
        }
    }
}

$usuarios = $db->query("SELECT * FROM usuario ORDER BY nome")->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="topbar">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 d-inline-block ms-2">
                <i class="bi bi-person-gear"></i> Usuários
            </h5>
        </div>
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalUsuario"
                onclick="limparForm()">
            <i class="bi bi-plus-lg"></i> Novo Usuário
        </button>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?> alert-auto"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-custom">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>E-mail</th>
                            <th>Perfil</th>
                            <th class="text-center">Ativo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($u['nome']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td>
                                <?php
                                $badgePerfil = [
                                    'ADMINISTRADOR' => 'bg-danger',
                                    'SECRETARIA'    => 'bg-primary',
                                    'VISUALIZADOR'  => 'bg-secondary',
                                ][$u['perfil']] ?? 'bg-secondary';
                                ?>
                                <span class="badge <?= $badgePerfil ?>"><?= htmlspecialchars($u['perfil']) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?= $u['ativo'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $u['ativo'] ? 'Sim' : 'Não' ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary"
                                        onclick='editarUsuario(<?= json_encode($u) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <?php if ($u['id_usuario'] != $_SESSION['usuario_id']): ?>
                                <form method="POST" class="d-inline">
                                    <?= csrf_input() ?>
                                    <input type="hidden" name="acao" value="excluir">
                                    <input type="hidden" name="id_usuario" value="<?= $u['id_usuario'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-confirm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Usuário -->
<div class="modal fade" id="modalUsuario" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title" id="modalUsuarioTitulo">
                    <i class="bi bi-person-plus"></i> Novo Usuário
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <?= csrf_input() ?>
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id_usuario" id="form_id_usuario">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="form_nome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mail *</label>
                        <input type="email" name="email" id="form_email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perfil *</label>
                        <select name="perfil" id="form_perfil" class="form-select" required>
                            <option value="SECRETARIA">Secretaria</option>
                            <option value="ADMINISTRADOR">Administrador</option>
                            <option value="VISUALIZADOR">Visualizador</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Senha <small class="text-muted" id="senhaHint">(obrigatória)</small></label>
                        <input type="password" name="senha" id="form_senha" class="form-control"
                               placeholder="Deixe em branco para manter a atual (ao editar)">
                    </div>
                    <div class="form-check">
                        <input type="checkbox" name="ativo" id="form_ativo" class="form-check-input" checked>
                        <label class="form-check-label" for="form_ativo">Usuário ativo</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary-custom">
                        <i class="bi bi-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function limparForm() {
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-person-plus"></i> Novo Usuário';
    document.getElementById('form_id_usuario').value = '';
    document.getElementById('form_nome').value = '';
    document.getElementById('form_email').value = '';
    document.getElementById('form_perfil').value = 'SECRETARIA';
    document.getElementById('form_senha').value = '';
    document.getElementById('form_ativo').checked = true;
    document.getElementById('senhaHint').textContent = '(obrigatória)';
}
function editarUsuario(u) {
    document.getElementById('modalUsuarioTitulo').innerHTML = '<i class="bi bi-pencil"></i> Editar Usuário';
    document.getElementById('form_id_usuario').value = u.id_usuario;
    document.getElementById('form_nome').value = u.nome;
    document.getElementById('form_email').value = u.email;
    document.getElementById('form_perfil').value = u.perfil;
    document.getElementById('form_senha').value = '';
    document.getElementById('form_ativo').checked = u.ativo == 1;
    document.getElementById('senhaHint').textContent = '(deixe em branco para manter)';
    new bootstrap.Modal(document.getElementById('modalUsuario')).show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
