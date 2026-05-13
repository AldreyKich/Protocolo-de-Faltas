<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/includes/auth.php';

$pageTitle = 'Dashboard';
$db = getDB();

// Contadores
$enviados    = $db->query("SELECT COUNT(*) FROM protocolo WHERE status='ENVIADO'")->fetchColumn();
$em_analise  = $db->query("SELECT COUNT(*) FROM protocolo WHERE status='EM_ANALISE'")->fetchColumn();
$aprovados   = $db->query("SELECT COUNT(*) FROM protocolo WHERE status='APROVADO'")->fetchColumn();
$rejeitados  = $db->query("SELECT COUNT(*) FROM protocolo WHERE status='REJEITADO'")->fetchColumn();

// Últimos protocolos
$stmt = $db->query("
    SELECT p.*, a.nome AS aluno_nome, a.turma
    FROM protocolo p
    INNER JOIN aluno a ON p.id_aluno = a.id_aluno
    ORDER BY p.data_envio DESC
    LIMIT 10
");
$ultimos = $stmt->fetchAll();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/sidebar.php';
?>

<div class="main-content">
    <div class="topbar">
        <div>
            <button class="btn btn-sm btn-outline-secondary d-md-none" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 d-inline-block ms-2">Dashboard</h5>
        </div>
        <div class="text-muted small">
            <i class="bi bi-calendar3"></i> <?= date('d/m/Y H:i') ?>
        </div>
    </div>

    <!-- Cards de estatísticas -->
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <i class="bi bi-inbox-fill stat-icon"></i>
                <div>
                    <div class="stat-value"><?= $enviados ?></div>
                    <div class="stat-label">Enviados</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <i class="bi bi-hourglass-split stat-icon"></i>
                <div>
                    <div class="stat-value"><?= $em_analise ?></div>
                    <div class="stat-label">Em Análise</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="bi bi-check-circle-fill stat-icon"></i>
                <div>
                    <div class="stat-value"><?= $aprovados ?></div>
                    <div class="stat-label">Aprovados</div>
                </div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="stat-card" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <i class="bi bi-x-circle-fill stat-icon"></i>
                <div>
                    <div class="stat-value"><?= $rejeitados ?></div>
                    <div class="stat-label">Rejeitados</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos protocolos -->
    <div class="card">
        <div class="card-header-primary">
            <i class="bi bi-clock-history"></i> Últimos Protocolos Enviados
        </div>
        <div class="card-body p-0">
            <?php if (empty($ultimos)): ?>
                <div class="p-4 text-center text-muted">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Nenhum protocolo registrado ainda.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Protocolo</th>
                                <th>Aluno</th>
                                <th>Turma</th>
                                <th>Data Falta</th>
                                <th>Enviado em</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ultimos as $p): ?>
                            <tr>
                                <td class="fw-bold text-primary"><?= htmlspecialchars($p['numero_protocolo']) ?></td>
                                <td><?= htmlspecialchars($p['aluno_nome']) ?></td>
                                <td><?= htmlspecialchars($p['turma']) ?></td>
                                <td><?= date('d/m/Y', strtotime($p['data_falta'])) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($p['data_envio'])) ?></td>
                                <td>
                                    <?php
                                    $badgeClass = [
                                        'ENVIADO'    => 'badge-enviado',
                                        'EM_ANALISE' => 'badge-em_analise',
                                        'APROVADO'   => 'badge-aprovado',
                                        'REJEITADO'  => 'badge-rejeitado',
                                    ][$p['status']] ?? 'bg-secondary';
                                    $statusLabel = str_replace('_', ' ', $p['status']);
                                    ?>
                                    <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
                                </td>
                                <td class="text-end">
                                    <a href="protocolo_detalhe.php?id=<?= $p['id_protocolo'] ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Ver
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
