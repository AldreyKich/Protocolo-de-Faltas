<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';

$numProtocolo = trim($_GET['protocolo'] ?? '');

if ($numProtocolo === '') {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit;
}

$db   = getDB();
$stmt = $db->prepare("
    SELECT p.*, a.nome AS aluno_nome, a.turma
    FROM protocolo p
    INNER JOIN aluno a ON p.id_aluno = a.id_aluno
    WHERE p.numero_protocolo = ?
");
$stmt->execute([$numProtocolo]);
$protocolo = $stmt->fetch();

if (!$protocolo) {
    header('Location: ' . BASE_URL . 'public/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protocolo Enviado — Protocolo Eletrônico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
</head>
<body>

<nav class="navbar navbar-public navbar-expand-lg navbar-dark">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>public/index.php">
            <i class="bi bi-file-earmark-check-fill me-2"></i>Protocolo Eletrônico
        </a>
    </div>
</nav>

<div class="container py-5">
    <div class="form-public text-center">
        <div class="card">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="fw-bold text-success mb-2">Protocolo Enviado com Sucesso!</h2>
                <p class="text-muted mb-4">
                    Sua justificativa foi registrada e será analisada pela secretaria.
                </p>

                <div class="mb-4">
                    <p class="text-muted small mb-1">Número do Protocolo</p>
                    <div class="protocolo-number"><?= htmlspecialchars($protocolo['numero_protocolo']) ?></div>
                </div>

                <div class="row g-3 text-start mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Aluno</small>
                            <strong><?= htmlspecialchars($protocolo['aluno_nome']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Turma</small>
                            <strong><?= htmlspecialchars($protocolo['turma']) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Data da Falta</small>
                            <strong><?= date('d/m/Y', strtotime($protocolo['data_falta'])) ?></strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded">
                            <small class="text-muted d-block">Enviado em</small>
                            <strong><?= date('d/m/Y H:i', strtotime($protocolo['data_envio'])) ?></strong>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info text-start">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>Guarde o número do protocolo!</strong> Você pode usá-lo para consultar o status
                    da sua justificativa a qualquer momento.
                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap mt-4">
                    <a href="<?= BASE_URL ?>public/consultar.php?protocolo=<?= urlencode($protocolo['numero_protocolo']) ?>"
                       class="btn btn-primary-custom">
                        <i class="bi bi-search me-2"></i>Consultar Status
                    </a>
                    <a href="<?= BASE_URL ?>public/novo_protocolo.php" class="btn btn-outline-secondary">
                        <i class="bi bi-plus-circle me-2"></i>Novo Protocolo
                    </a>
                    <a href="<?= BASE_URL ?>public/index.php" class="btn btn-outline-secondary">
                        <i class="bi bi-house me-2"></i>Início
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
