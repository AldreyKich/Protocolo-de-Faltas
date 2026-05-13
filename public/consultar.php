<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';

$db         = getDB();
$protocolo  = null;
$anexos     = [];
$numBusca   = trim($_GET['protocolo'] ?? '');
$erro       = '';

if ($numBusca !== '') {
    $stmt = $db->prepare("
        SELECT p.*, a.nome AS aluno_nome, a.turma, a.matricula,
               r.nome AS resp_nome
        FROM protocolo p
        INNER JOIN aluno a ON p.id_aluno = a.id_aluno
        INNER JOIN responsavel r ON p.id_responsavel = r.id_responsavel
        WHERE p.numero_protocolo = ?
    ");
    $stmt->execute([$numBusca]);
    $protocolo = $stmt->fetch();

    if (!$protocolo) {
        $erro = 'Protocolo não encontrado. Verifique o número informado.';
    } else {
        $stmtA = $db->prepare("SELECT * FROM anexo WHERE id_protocolo = ?");
        $stmtA->execute([$protocolo['id_protocolo']]);
        $anexos = $stmtA->fetchAll();
    }
}

$statusInfo = [
    'ENVIADO'    => ['class' => 'badge-enviado',    'icon' => 'bi-inbox',           'label' => 'Enviado',    'desc' => 'Seu protocolo foi recebido e aguarda análise da secretaria.'],
    'EM_ANALISE' => ['class' => 'badge-em_analise', 'icon' => 'bi-hourglass-split', 'label' => 'Em Análise', 'desc' => 'A secretaria está analisando sua justificativa.'],
    'APROVADO'   => ['class' => 'badge-aprovado',   'icon' => 'bi-check-circle',    'label' => 'Aprovado',   'desc' => 'Sua justificativa foi aprovada e a falta foi justificada.'],
    'REJEITADO'  => ['class' => 'badge-rejeitado',  'icon' => 'bi-x-circle',        'label' => 'Rejeitado',  'desc' => 'Sua justificativa foi rejeitada. Veja as observações abaixo.'],
];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultar Protocolo — Protocolo Eletrônico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Space+Grotesk:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>assets/css/style.css" rel="stylesheet">
    <style>
        :root {
            --page-bg: #0a0e1a;
            --surface: #ffffff;
            --ink: #0f172a;
            --brand: #3b82f6;
            --brand-dark: #2563eb;
            --accent: #10b981;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            overflow-x: hidden;
        }

        body {
            font-family: "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            color: var(--ink);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }

        .animated-bg::before,
        .animated-bg::after {
            content: "";
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            animation: float 20s ease-in-out infinite;
        }

        .animated-bg::before {
            width: 500px;
            height: 500px;
            background: var(--brand);
            top: -100px;
            left: -100px;
        }

        .animated-bg::after {
            width: 400px;
            height: 400px;
            background: var(--accent);
            bottom: -100px;
            right: -100px;
            animation-delay: 10s;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(50px, -50px) scale(1.1); }
            66% { transform: translate(-50px, 50px) scale(0.9); }
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
            flex: 1;
        }

        /* Navbar */
        .navbar-modern {
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 0.75rem 0;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .navbar-brand {
            font-size: 1.15rem;
            font-weight: 800;
            color: #fff !important;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover {
            transform: translateY(-2px);
        }

        .navbar-brand i {
            font-size: 1.3rem;
            color: var(--brand);
        }

        .btn-nav {
            border-radius: 8px;
            padding: 0.4rem 0.9rem;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 1px solid transparent;
        }

        .btn-nav-outline {
            color: #fff;
            border-color: rgba(255, 255, 255, 0.3);
        }

        .btn-nav-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-2px);
            color: #fff;
        }

        /* Search Panel */
        .search-container {
            max-width: 900px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .search-panel {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            animation: fadeInUp 0.8s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-title {
            font-family: "Space Grotesk", sans-serif;
            font-size: 1.8rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .search-subtitle {
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 0.8rem 1.2rem;
            background: rgba(255, 255, 255, 0.95);
            font-size: 1rem;
            font-family: "Consolas", monospace;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2);
            background: #fff;
        }

        .btn-search {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 12px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.5);
            color: #fff;
        }

        /* Result Card */
        .result-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
        }

        .result-body {
            padding: 2rem;
        }

        .status-highlight {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 2px solid #e5e7eb;
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            margin-bottom: 2rem;
        }

        .status-icon {
            font-size: 3.5rem;
            margin-bottom: 1rem;
        }

        .badge {
            padding: 0.6rem 1.5rem;
            font-size: 1.1rem;
            font-weight: 700;
            border-radius: 50px;
        }

        .badge-enviado {
            background: #6b7280;
            color: #fff;
        }

        .badge-em_analise {
            background: #f59e0b;
            color: #fff;
        }

        .badge-aprovado {
            background: #10b981;
            color: #fff;
        }

        .badge-rejeitado {
            background: #ef4444;
            color: #fff;
        }

        .info-label {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.3rem;
        }

        .info-value {
            font-size: 1rem;
            color: var(--ink);
            font-weight: 600;
        }

        .text-protocolo {
            font-family: "Consolas", monospace;
            font-size: 1.3rem;
            color: var(--brand);
            font-weight: 800;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            border: none;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
            color: #fff;
        }

        .btn-outline-secondary {
            border: 2px solid #6b7280;
            color: #6b7280;
            padding: 0.7rem 1.5rem;
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background: #6b7280;
            color: #fff;
            transform: translateY(-2px);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 1rem 1.5rem;
        }

        .motivo-box {
            background: #f8fafc;
            border-left: 4px solid var(--brand);
            padding: 1rem 1.5rem;
            border-radius: 8px;
        }

        .obs-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem 1.5rem;
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .search-panel {
                padding: 1.5rem;
            }

            .result-body {
                padding: 1.5rem;
            }

            .search-title {
                font-size: 1.4rem;
            }

            .navbar-modern .d-flex {
                flex-direction: column;
                width: 100%;
            }

            .btn-nav {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background -->
    <div class="animated-bg"></div>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Navbar -->
        <nav class="navbar-modern">
            <div class="container">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <a class="navbar-brand" href="<?= BASE_URL ?>public/index.php">
                        <i class="bi bi-file-earmark-check-fill"></i>
                        Protocolo Eletrônico
                    </a>
                    <div>
                        <a href="<?= BASE_URL ?>public/novo_protocolo.php" class="btn btn-nav btn-nav-outline">
                            <i class="bi bi-plus-circle"></i> Nova Justificativa
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Search Container -->
        <div class="search-container">
            <section class="search-panel">
                <h4 class="search-title">
                    <i class="bi bi-search"></i>
                    Consultar Status do Protocolo
                </h4>
                <p class="search-subtitle">Informe o número gerado no envio para acompanhar a análise.</p>
                <form method="GET" class="row g-3">
                    <div class="col-md">
                        <input type="text" name="protocolo" class="form-control"
                               placeholder="Ex: PROT-20260506-AB1C2"
                               value="<?= htmlspecialchars($numBusca) ?>"
                               required>
                    </div>
                    <div class="col-md-auto">
                        <button type="submit" class="btn btn-search w-100">
                            <i class="bi bi-search me-2"></i>Consultar
                        </button>
                    </div>
                </form>
            </section>

            <?php if ($erro): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($erro) ?>
                </div>
            <?php endif; ?>

            <?php if ($protocolo):
                $si = $statusInfo[$protocolo['status']] ?? $statusInfo['ENVIADO'];
                $statusColors = [
                    'ENVIADO' => '#6b7280',
                    'EM_ANALISE' => '#f59e0b',
                    'APROVADO' => '#10b981',
                    'REJEITADO' => '#ef4444'
                ];
                $statusColor = $statusColors[$protocolo['status']] ?? '#6b7280';
            ?>
            <div class="result-card">
                <div class="result-body">
                    <!-- Status em destaque -->
                    <div class="status-highlight">
                        <i class="bi <?= $si['icon'] ?> status-icon d-block"
                           style="color: <?= $statusColor ?>"></i>
                        <span class="badge <?= $si['class'] ?> mb-3">
                            <?= htmlspecialchars($si['label']) ?>
                        </span>
                        <p class="text-muted mb-0"><?= htmlspecialchars($si['desc']) ?></p>
                    </div>

                    <!-- Dados -->
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="info-label">Número do Protocolo</div>
                            <div class="text-protocolo"><?= htmlspecialchars($protocolo['numero_protocolo']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Aluno</div>
                            <div class="info-value"><?= htmlspecialchars($protocolo['aluno_nome']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Turma</div>
                            <div class="info-value"><?= htmlspecialchars($protocolo['turma']) ?></div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-label">Matrícula</div>
                            <div class="info-value"><?= htmlspecialchars($protocolo['matricula']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Data da Falta</div>
                            <div class="info-value"><?= date('d/m/Y', strtotime($protocolo['data_falta'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">Enviado em</div>
                            <div class="info-value"><?= date('d/m/Y H:i', strtotime($protocolo['data_envio'])) ?></div>
                        </div>
                        <div class="col-12">
                            <div class="info-label">Motivo</div>
                            <div class="motivo-box"><?= nl2br(htmlspecialchars($protocolo['motivo'])) ?></div>
                        </div>
                        <?php if ($protocolo['observacoes']): ?>
                        <div class="col-12">
                            <div class="info-label">Observações da Secretaria</div>
                            <div class="obs-box">
                                <?= nl2br(htmlspecialchars($protocolo['observacoes'])) ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <?php if (!empty($anexos)): ?>
                    <hr class="my-4">
                    <h6 class="fw-bold mb-3" style="color: var(--brand);">
                        <i class="bi bi-paperclip"></i> Anexos
                    </h6>
                    <?php foreach ($anexos as $a): ?>
                        <div class="d-flex align-items-center gap-3 p-3 mb-2" style="background: #f8fafc; border-radius: 10px;">
                            <i class="bi bi-file-earmark fs-4" style="color: var(--brand);"></i>
                            <span class="fw-medium"><?= htmlspecialchars($a['nome_arquivo']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="mt-4 d-flex gap-3 flex-wrap">
                        <a href="<?= BASE_URL ?>public/index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-house"></i> Início
                        </a>
                        <a href="<?= BASE_URL ?>public/novo_protocolo.php" class="btn btn-primary-custom">
                            <i class="bi bi-plus-circle"></i> Novo Protocolo
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
