<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Detalhe do Protocolo';
$db = getDB();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: protocolos.php');
    exit;
}

$stmt = $db->prepare("
    SELECT p.*,
           a.nome AS aluno_nome, a.matricula, a.turma, a.email AS aluno_email, a.telefone AS aluno_tel,
           r.nome AS resp_nome, r.cpf AS resp_cpf, r.telefone AS resp_tel, r.email AS resp_email,
           u.nome AS usuario_nome
    FROM protocolo p
    INNER JOIN aluno a ON p.id_aluno = a.id_aluno
    INNER JOIN responsavel r ON p.id_responsavel = r.id_responsavel
    LEFT  JOIN usuario u ON p.id_usuario = u.id_usuario
    WHERE p.id_protocolo = ?
");
$stmt->execute([$id]);
$p = $stmt->fetch();

if (!$p) {
    header('Location: protocolos.php');
    exit;
}

// Anexos
$anexos = $db->prepare("SELECT * FROM anexo WHERE id_protocolo = ?");
$anexos->execute([$id]);
$anexos = $anexos->fetchAll();

// Processar mudança de status
$msg = '';
$msgType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_status'])) {
    if ($_SESSION['usuario_perfil'] === 'VISUALIZADOR') {
        http_response_code(403);
        die('Acesso negado.');
    }

    if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
        $msg = 'Sessão expirada. Recarregue a página e tente novamente.';
        $msgType = 'danger';
        unset($_POST['novo_status']);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['novo_status'])) {
    $novoStatus = $_POST['novo_status'];
    $obs        = trim($_POST['observacoes'] ?? '');
    $statusValidos = ['ENVIADO', 'EM_ANALISE', 'APROVADO', 'REJEITADO'];

    if (in_array($novoStatus, $statusValidos, true)) {
        $upd = $db->prepare("
            UPDATE protocolo
            SET status = ?, observacoes = ?, id_usuario = ?
            WHERE id_protocolo = ?
        ");
        $upd->execute([$novoStatus, $obs, $_SESSION['usuario_id'], $id]);
        $msg = 'Status atualizado com sucesso!';
        // Recarregar dados
        $stmt->execute([$id]);
        $p = $stmt->fetch();
    } else {
        $msg = 'Status inválido.';
        $msgType = 'danger';
    }
}

$badgeClass = [
    'ENVIADO'    => 'badge-enviado',
    'EM_ANALISE' => 'badge-em_analise',
    'APROVADO'   => 'badge-aprovado',
    'REJEITADO'  => 'badge-rejeitado',
][$p['status']] ?? 'bg-secondary';
$statusLabel = str_replace('_', ' ', $p['status']);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="topbar">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <a href="protocolos.php" class="btn btn-sm btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i>
            </a>
            <h5 class="mb-0 d-inline-block">Protocolo <?= htmlspecialchars($p['numero_protocolo']) ?></h5>
        </div>
        <span class="badge <?= $badgeClass ?> fs-6"><?= htmlspecialchars($statusLabel) ?></span>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msgType ?> alert-auto">
            <i class="bi bi-check-circle"></i> <?= htmlspecialchars($msg) ?>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Dados do protocolo -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header-primary">
                    <i class="bi bi-file-earmark-text"></i> Dados do Protocolo
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Número do Protocolo</label>
                            <div class="fw-bold text-primary fs-5"><?= htmlspecialchars($p['numero_protocolo']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Data de Envio</label>
                            <div><?= date('d/m/Y H:i', strtotime($p['data_envio'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Data da Falta</label>
                            <div class="fw-bold"><?= date('d/m/Y', strtotime($p['data_falta'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Analisado por</label>
                            <div><?= $p['usuario_nome'] ? htmlspecialchars($p['usuario_nome']) : '<span class="text-muted">—</span>' ?></div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Motivo / Justificativa</label>
                            <div class="p-3 bg-light rounded"><?= nl2br(htmlspecialchars($p['motivo'])) ?></div>
                        </div>
                        <?php if ($p['observacoes']): ?>
                        <div class="col-12">
                            <label class="text-muted small">Observações da Secretaria</label>
                            <div class="p-3 bg-light rounded"><?= nl2br(htmlspecialchars($p['observacoes'])) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Dados do Aluno -->
            <div class="card mb-4">
                <div class="card-header-primary">
                    <i class="bi bi-person-badge"></i> Dados do Aluno
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nome</label>
                            <div class="fw-bold"><?= htmlspecialchars($p['aluno_nome']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Matrícula</label>
                            <div><?= htmlspecialchars($p['matricula']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <label class="text-muted small">Turma</label>
                            <div><?= htmlspecialchars($p['turma']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">E-mail</label>
                            <div><?= $p['aluno_email'] ? htmlspecialchars($p['aluno_email']) : '—' ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Telefone</label>
                            <div><?= $p['aluno_tel'] ? htmlspecialchars($p['aluno_tel']) : '—' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Dados do Responsável -->
            <div class="card mb-4">
                <div class="card-header-primary">
                    <i class="bi bi-people"></i> Dados do Responsável
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="text-muted small">Nome</label>
                            <div class="fw-bold"><?= htmlspecialchars($p['resp_nome']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">CPF</label>
                            <div><?= htmlspecialchars($p['resp_cpf']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">E-mail</label>
                            <div><?= $p['resp_email'] ? htmlspecialchars($p['resp_email']) : '—' ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small">Telefone</label>
                            <div><?= $p['resp_tel'] ? htmlspecialchars($p['resp_tel']) : '—' ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anexos -->
            <div class="card">
                <div class="card-header-primary">
                    <i class="bi bi-paperclip"></i> Anexos
                </div>
                <div class="card-body">
                    <?php if (empty($anexos)): ?>
                        <p class="text-muted mb-0">Nenhum anexo enviado.</p>
                    <?php else: ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($anexos as $a): ?>
                                <div class="list-group-item d-flex align-items-center justify-content-between">
                                    <div>
                                        <i class="bi bi-file-earmark me-2 text-primary"></i>
                                        <?= htmlspecialchars($a['nome_arquivo']) ?>
                                        <small class="text-muted ms-2"><?= htmlspecialchars($a['tipo_arquivo'] ?? '') ?></small>
                                    </div>
                                    <a href="<?= BASE_URL ?>admin/anexo.php?id=<?= (int) $a['id_anexo'] ?>"
                                       target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-download"></i> Abrir
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Painel de ação -->
        <div class="col-lg-4">
            <?php if ($_SESSION['usuario_perfil'] !== 'VISUALIZADOR'): ?>
            <div class="card sticky-top" style="top: 1rem;">
                <div class="card-header-primary">
                    <i class="bi bi-pencil-square"></i> Atualizar Status
                </div>
                <div class="card-body">
                    <form method="POST">
                        <?= csrf_input() ?>
                        <div class="mb-3">
                            <label class="form-label">Novo Status</label>
                            <select name="novo_status" class="form-select" required>
                                <option value="ENVIADO"    <?= $p['status']==='ENVIADO'    ? 'selected':'' ?>>Enviado</option>
                                <option value="EM_ANALISE" <?= $p['status']==='EM_ANALISE' ? 'selected':'' ?>>Em Análise</option>
                                <option value="APROVADO"   <?= $p['status']==='APROVADO'   ? 'selected':'' ?>>Aprovado</option>
                                <option value="REJEITADO"  <?= $p['status']==='REJEITADO'  ? 'selected':'' ?>>Rejeitado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Observações</label>
                            <textarea name="observacoes" class="form-control" rows="4"
                                      placeholder="Observações para o responsável..."><?= htmlspecialchars($p['observacoes'] ?? '') ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="bi bi-save"></i> Salvar
                        </button>
                    </form>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
