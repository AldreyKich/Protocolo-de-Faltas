<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';
requirePerfil(['ADMINISTRADOR', 'SECRETARIA']);

$pageTitle = 'Alunos';
$db  = getDB();
$msg = '';
$msgType = 'success';

// ---- Ações POST ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $msg = 'Sessão expirada. Recarregue a página e tente novamente.';
        $msgType = 'danger';
        $_POST['acao'] = '';
    }

    $acao = $_POST['acao'] ?? '';

    if ($acao === 'salvar') {
        $id       = filter_input(INPUT_POST, 'id_aluno', FILTER_VALIDATE_INT) ?: null;
        $nome     = trim($_POST['nome'] ?? '');
        $matricula= trim($_POST['matricula'] ?? '');
        $turma    = trim($_POST['turma'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $ativo    = isset($_POST['ativo']) ? 1 : 0;

        if ($nome === '' || $matricula === '' || $turma === '') {
            $msg = 'Nome, matrícula e turma são obrigatórios.';
            $msgType = 'danger';
        } else {
            try {
                if ($id) {
                    $stmt = $db->prepare("UPDATE aluno SET nome=?, matricula=?, turma=?, email=?, telefone=?, ativo=? WHERE id_aluno=?");
                    $stmt->execute([$nome, $matricula, $turma, $email ?: null, $telefone ?: null, $ativo, $id]);
                    $msg = 'Aluno atualizado com sucesso!';
                } else {
                    $stmt = $db->prepare("INSERT INTO aluno (nome, matricula, turma, email, telefone, ativo) VALUES (?,?,?,?,?,?)");
                    $stmt->execute([$nome, $matricula, $turma, $email ?: null, $telefone ?: null, $ativo]);
                    $msg = 'Aluno cadastrado com sucesso!';
                }
            } catch (PDOException $e) {
                $msg = 'Erro: matrícula já cadastrada.';
                $msgType = 'danger';
            }
        }
    }

    if ($acao === 'excluir') {
        $id = filter_input(INPUT_POST, 'id_aluno', FILTER_VALIDATE_INT);
        if ($id) {
            // Verifica se tem protocolos
            $check = $db->prepare("SELECT COUNT(*) FROM protocolo WHERE id_aluno = ?");
            $check->execute([$id]);
            if ($check->fetchColumn() > 0) {
                $msg = 'Não é possível excluir: aluno possui protocolos vinculados.';
                $msgType = 'danger';
            } else {
                $db->prepare("DELETE FROM aluno WHERE id_aluno = ?")->execute([$id]);
                $msg = 'Aluno excluído.';
            }
        }
    }
}

// Editar
$editando = null;
$editId = filter_input(INPUT_GET, 'editar', FILTER_VALIDATE_INT);
if ($editId) {
    $s = $db->prepare("SELECT * FROM aluno WHERE id_aluno = ?");
    $s->execute([$editId]);
    $editando = $s->fetch();
}

// Listagem
$busca = trim($_GET['busca'] ?? '');
if ($busca !== '') {
    $stmt = $db->prepare("SELECT * FROM aluno WHERE nome LIKE ? OR matricula LIKE ? OR turma LIKE ? ORDER BY nome");
    $stmt->execute(["%$busca%", "%$busca%", "%$busca%"]);
} else {
    $stmt = $db->query("SELECT * FROM aluno ORDER BY nome");
}
$alunos = $stmt->fetchAll();

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
                <i class="bi bi-people"></i> Alunos
            </h5>
        </div>
        <button class="btn btn-primary-custom btn-sm" data-bs-toggle="modal" data-bs-target="#modalAluno"
                onclick="limparForm()">
            <i class="bi bi-plus-lg"></i> Novo Aluno
        </button>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?> alert-auto">
            <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <!-- Busca -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2">
                <input type="text" name="busca" class="form-control form-control-sm"
                       placeholder="Buscar por nome, matrícula ou turma..."
                       value="<?= htmlspecialchars($busca) ?>">
                <button type="submit" class="btn btn-primary-custom btn-sm">
                    <i class="bi bi-search"></i>
                </button>
                <?php if ($busca): ?>
                    <a href="alunos.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 table-custom">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Matrícula</th>
                            <th>Turma</th>
                            <th>E-mail</th>
                            <th>Telefone</th>
                            <th class="text-center">Ativo</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($alunos)): ?>
                            <tr><td colspan="7" class="text-center text-muted py-4">Nenhum aluno encontrado.</td></tr>
                        <?php else: ?>
                            <?php foreach ($alunos as $a): ?>
                            <tr>
                                <td class="fw-bold"><?= htmlspecialchars($a['nome']) ?></td>
                                <td><?= htmlspecialchars($a['matricula']) ?></td>
                                <td><?= htmlspecialchars($a['turma']) ?></td>
                                <td><?= $a['email'] ? htmlspecialchars($a['email']) : '—' ?></td>
                                <td><?= $a['telefone'] ? htmlspecialchars($a['telefone']) : '—' ?></td>
                                <td class="text-center">
                                    <?php if ($a['ativo']): ?>
                                        <span class="badge bg-success">Sim</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary"
                                            onclick='editarAluno(<?= json_encode($a) ?>)'>
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <?php if ($_SESSION['usuario_perfil'] === 'ADMINISTRADOR'): ?>
                                    <form method="POST" class="d-inline">
                                        <?= csrf_input() ?>
                                        <input type="hidden" name="acao" value="excluir">
                                        <input type="hidden" name="id_aluno" value="<?= $a['id_aluno'] ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-danger btn-delete-confirm">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Aluno -->
<div class="modal fade" id="modalAluno" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header card-header-primary">
                <h5 class="modal-title" id="modalAlunoTitulo">
                    <i class="bi bi-person-plus"></i> Novo Aluno
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <?= csrf_input() ?>
                <input type="hidden" name="acao" value="salvar">
                <input type="hidden" name="id_aluno" id="form_id_aluno">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome *</label>
                        <input type="text" name="nome" id="form_nome" class="form-control" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Matrícula *</label>
                            <input type="text" name="matricula" id="form_matricula" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Turma *</label>
                            <input type="text" name="turma" id="form_turma" class="form-control"
                                   placeholder="Ex: 3A, 2B..." required>
                        </div>
                    </div>
                    <div class="row g-2 mt-1">
                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" id="form_email" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="telefone" id="form_telefone" class="form-control mask-tel">
                        </div>
                    </div>
                    <div class="form-check mt-3">
                        <input type="checkbox" name="ativo" id="form_ativo" class="form-check-input" checked>
                        <label class="form-check-label" for="form_ativo">Aluno ativo</label>
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
    document.getElementById('modalAlunoTitulo').innerHTML = '<i class="bi bi-person-plus"></i> Novo Aluno';
    document.getElementById('form_id_aluno').value = '';
    document.getElementById('form_nome').value = '';
    document.getElementById('form_matricula').value = '';
    document.getElementById('form_turma').value = '';
    document.getElementById('form_email').value = '';
    document.getElementById('form_telefone').value = '';
    document.getElementById('form_ativo').checked = true;
}

function editarAluno(a) {
    document.getElementById('modalAlunoTitulo').innerHTML = '<i class="bi bi-pencil"></i> Editar Aluno';
    document.getElementById('form_id_aluno').value = a.id_aluno;
    document.getElementById('form_nome').value = a.nome;
    document.getElementById('form_matricula').value = a.matricula;
    document.getElementById('form_turma').value = a.turma;
    document.getElementById('form_email').value = a.email || '';
    document.getElementById('form_telefone').value = a.telefone || '';
    document.getElementById('form_ativo').checked = a.ativo == 1;
    new bootstrap.Modal(document.getElementById('modalAluno')).show();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
