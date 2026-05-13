<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Protocolos';
$db = getDB();

// Filtros
$filtroStatus = $_GET['status'] ?? '';
$filtroTurma  = $_GET['turma']  ?? '';
$filtroData   = $_GET['data']   ?? '';
$filtroBusca  = trim($_GET['busca'] ?? '');

$where  = ['1=1'];
$params = [];

if ($filtroStatus !== '') {
    $where[]  = 'p.status = ?';
    $params[] = $filtroStatus;
}
if ($filtroTurma !== '') {
    $where[]  = 'a.turma = ?';
    $params[] = $filtroTurma;
}
if ($filtroData !== '') {
    $where[]  = 'p.data_falta = ?';
    $params[] = $filtroData;
}
if ($filtroBusca !== '') {
    $where[]  = '(a.nome LIKE ? OR p.numero_protocolo LIKE ? OR r.nome LIKE ?)';
    $like = '%' . $filtroBusca . '%';
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
}

$sql = "
    SELECT p.*, a.nome AS aluno_nome, a.turma, r.nome AS responsavel_nome
    FROM protocolo p
    INNER JOIN aluno a ON p.id_aluno = a.id_aluno
    INNER JOIN responsavel r ON p.id_responsavel = r.id_responsavel
    WHERE " . implode(' AND ', $where) . "
    ORDER BY p.data_envio DESC
";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$protocolos = $stmt->fetchAll();

// Turmas para filtro
$turmas = $db->query("SELECT DISTINCT turma FROM aluno ORDER BY turma")->fetchAll(PDO::FETCH_COLUMN);

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
                <i class="bi bi-file-earmark-text"></i> Protocolos
            </h5>
        </div>
        <span class="badge bg-secondary"><?= count($protocolos) ?> registro(s)</span>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Buscar</label>
                    <input type="text" name="busca" class="form-control form-control-sm"
                           placeholder="Aluno, protocolo, responsável..."
                           value="<?= htmlspecialchars($filtroBusca) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <option value="ENVIADO"    <?= $filtroStatus==='ENVIADO'    ? 'selected':'' ?>>Enviado</option>
                        <option value="EM_ANALISE" <?= $filtroStatus==='EM_ANALISE' ? 'selected':'' ?>>Em Análise</option>
                        <option value="APROVADO"   <?= $filtroStatus==='APROVADO'   ? 'selected':'' ?>>Aprovado</option>
                        <option value="REJEITADO"  <?= $filtroStatus==='REJEITADO'  ? 'selected':'' ?>>Rejeitado</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Turma</label>
                    <select name="turma" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach ($turmas as $t): ?>
                            <option value="<?= htmlspecialchars($t) ?>" <?= $filtroTurma===$t ? 'selected':'' ?>>
                                <?= htmlspecialchars($t) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Data da Falta</label>
                    <input type="date" name="data" class="form-control form-control-sm"
                           value="<?= htmlspecialchars($filtroData) ?>">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-custom btn-sm">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="protocolos.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body p-0">
            <?php if (empty($protocolos)): ?>
                <div class="p-5 text-center text-muted">
                    <i class="bi bi-search fs-1 d-block mb-2"></i>
                    Nenhum protocolo encontrado com os filtros aplicados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 table-custom">
                        <thead>
                            <tr>
                                <th>Nº Protocolo</th>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Responsável</th>
                                <th>Data Falta</th>
                                <th>Enviado em</th>
                                <th>Status</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($protocolos as $p):
                                $badgeClass = [
                                    'ENVIADO'    => 'badge-enviado',
                                    'EM_ANALISE' => 'badge-em_analise',
                                    'APROVADO'   => 'badge-aprovado',
                                    'REJEITADO'  => 'badge-rejeitado',
                                ][$p['status']] ?? 'bg-secondary';
                                $statusLabel = str_replace('_', ' ', $p['status']);
                            ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($p['numero_protocolo']) ?></td>
                                <td><?= htmlspecialchars($p['aluno_nome']) ?></td>
                                <td><?= htmlspecialchars($p['turma']) ?></td>
                                <td><?= htmlspecialchars($p['responsavel_nome']) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['data_falta'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['data_envio'])) ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span></td>
                                <td class="text-center">
                                    <a href="protocolo_detalhe.php?id=<?= $p['id_protocolo'] ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="tooltip" title="Ver detalhes">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
