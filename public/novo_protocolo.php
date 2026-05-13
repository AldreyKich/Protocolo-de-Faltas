<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';

$db     = getDB();
$erros  = [];
$dados  = [];

// Buscar alunos ativos para o select
$alunos = $db->query("SELECT id_aluno, nome, matricula, turma FROM aluno WHERE ativo = 1 ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Coletar dados
    $dados['id_aluno']    = filter_input(INPUT_POST, 'id_aluno', FILTER_VALIDATE_INT);
    $dados['data_falta']  = trim($_POST['data_falta'] ?? '');
    $dados['motivo']      = trim($_POST['motivo'] ?? '');
    $dados['resp_nome']   = trim($_POST['resp_nome'] ?? '');
    $dados['resp_cpf']    = preg_replace('/\D/', '', $_POST['resp_cpf'] ?? '');
    $dados['resp_tel']    = trim($_POST['resp_tel'] ?? '');
    $dados['resp_email']  = trim($_POST['resp_email'] ?? '');

    // Validações
    if (!$dados['id_aluno'])          $erros[] = 'Selecione o aluno.';
    if ($dados['data_falta'] === '')  $erros[] = 'Informe a data da falta.';
    if ($dados['motivo'] === '')      $erros[] = 'Descreva o motivo da falta.';
    if ($dados['resp_nome'] === '')   $erros[] = 'Informe o nome do responsável.';
    if (strlen($dados['resp_cpf']) !== 11) $erros[] = 'CPF do responsável inválido.';

    // Validar data (não pode ser futura)
    if ($dados['data_falta'] !== '' && strtotime($dados['data_falta']) > strtotime('today')) {
        $erros[] = 'A data da falta não pode ser futura.';
    }

    // Upload do anexo
    $anexoNome    = null;
    $anexoCaminho = null;
    $anexoTipo    = null;

    if (isset($_FILES['anexo']) && $_FILES['anexo']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['anexo'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, ALLOWED_TYPES, true)) {
            $erros[] = 'Tipo de arquivo não permitido. Use PDF, JPG ou PNG.';
        } elseif ($file['size'] > MAX_FILE_SIZE) {
            $erros[] = 'Arquivo muito grande. Máximo 5 MB.';
        } else {
            $ext          = pathinfo($file['name'], PATHINFO_EXTENSION);
            $anexoCaminho = uniqid('atestado_', true) . '.' . $ext;
            $anexoNome    = $file['name'];
            $anexoTipo    = $mimeType;
        }
    }

    if (empty($erros)) {
        try {
            $db->beginTransaction();

            // Responsável: buscar por CPF ou inserir
            $stmtR = $db->prepare("SELECT id_responsavel FROM responsavel WHERE cpf = ?");
            $stmtR->execute([$dados['resp_cpf']]);
            $resp = $stmtR->fetch();

            if ($resp) {
                $idResp = $resp['id_responsavel'];
                // Atualizar dados se necessário
                $db->prepare("UPDATE responsavel SET nome=?, telefone=?, email=? WHERE id_responsavel=?")
                   ->execute([$dados['resp_nome'], $dados['resp_tel'] ?: null, $dados['resp_email'] ?: null, $idResp]);
            } else {
                $db->prepare("INSERT INTO responsavel (nome, cpf, telefone, email) VALUES (?,?,?,?)")
                   ->execute([$dados['resp_nome'], $dados['resp_cpf'], $dados['resp_tel'] ?: null, $dados['resp_email'] ?: null]);
                $idResp = $db->lastInsertId();
            }

            // Gerar número de protocolo: PROT-YYYYMMDD-XXXXX
            $numProtocolo = 'PROT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            // Inserir protocolo
            $db->prepare("
                INSERT INTO protocolo (numero_protocolo, data_falta, motivo, id_aluno, id_responsavel)
                VALUES (?, ?, ?, ?, ?)
            ")->execute([$numProtocolo, $dados['data_falta'], $dados['motivo'], $dados['id_aluno'], $idResp]);
            $idProtocolo = $db->lastInsertId();

            // Salvar arquivo
            if ($anexoCaminho) {
                move_uploaded_file($_FILES['anexo']['tmp_name'], UPLOAD_DIR . $anexoCaminho);
                $db->prepare("INSERT INTO anexo (nome_arquivo, caminho_arquivo, tipo_arquivo, id_protocolo) VALUES (?,?,?,?)")
                   ->execute([$anexoNome, $anexoCaminho, $anexoTipo, $idProtocolo]);
            }

            $db->commit();

            // Redirecionar para confirmação
            header('Location: ' . BASE_URL . 'public/confirmacao.php?protocolo=' . urlencode($numProtocolo));
            exit;

        } catch (Exception $e) {
            $db->rollBack();
            $erros[] = 'Erro ao registrar protocolo. Tente novamente.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Justificativa — Protocolo Eletrônico</title>
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
            max-width: 100vw;
        }

        body {
            font-family: "Manrope", -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #0a0e1a 0%, #1a1f35 100%);
            color: var(--ink);
            line-height: 1.6;
            display: flex;
            flex-direction: column;
        }

        * {
            box-sizing: border-box;
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
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
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

        /* Form Container */
        .form-container {
            max-width: 100%;
            width: 100%;
            margin: 1rem auto;
            padding: 0 0.5rem;
            box-sizing: border-box;
        }

        .form-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            overflow: hidden;
            animation: fadeInUp 0.8s ease;
            max-width: 100%;
            box-sizing: border-box;
        }

        @media (min-width: 768px) {
            .form-container {
                max-width: 95%;
                padding: 0 1rem;
            }
        }

        @media (min-width: 1200px) {
            .form-container {
                max-width: 1000px;
            }
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

        .form-header {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            padding: 1rem;
            border-bottom: 3px solid rgba(255, 255, 255, 0.2);
        }

        .form-header h4 {
            font-family: "Space Grotesk", sans-serif;
            font-size: 1.2rem;
            font-weight: 800;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-header small {
            opacity: 0.9;
            font-size: 0.8rem;
        }

        .form-body {
            padding: 1rem;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .row {
            margin-left: 0;
            margin-right: 0;
        }

        .row > * {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .section-title {
            color: var(--brand);
            font-weight: 800;
            font-size: 1rem;
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-divider {
            border: none;
            height: 2px;
            background: linear-gradient(90deg, var(--brand) 0%, transparent 100%);
            margin: 0.8rem 0;
        }

        .form-label {
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
        }

        .form-control,
        .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            padding: 0.5rem 0.8rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .upload-area {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 3px dashed #cbd5e1;
            border-radius: 16px;
            padding: 1rem;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .upload-area:hover {
            border-color: var(--brand);
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            transform: translateY(-2px);
        }

        .upload-area i {
            font-size: 2rem;
            color: var(--brand);
            margin-bottom: 0.5rem;
        }

        .upload-area p {
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        .upload-area small {
            font-size: 0.8rem;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            border: none;
            padding: 0.6rem 1.3rem;
            border-radius: 10px;
            font-weight: 700;
            font-size: 0.9rem;
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
            padding: 0.6rem 1.3rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
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
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .form-container {
                margin: 0.5rem auto;
                padding: 0 0.25rem;
            }

            .form-body {
                padding: 0.8rem;
            }

            .form-header {
                padding: 0.8rem;
            }

            .form-header h4 {
                font-size: 1rem;
            }

            .form-header small {
                font-size: 0.75rem;
            }

            .navbar-modern .d-flex {
                flex-direction: column;
                width: 100%;
            }

            .btn-nav {
                width: 100%;
                text-align: center;
            }

            .upload-area {
                padding: 0.8rem;
            }

            .upload-area i {
                font-size: 1.8rem;
            }

            .row > * {
                padding-left: 0.25rem;
                padding-right: 0.25rem;
            }

            .form-control,
            .form-select {
                font-size: 0.85rem;
                padding: 0.4rem 0.6rem;
            }

            .section-title {
                font-size: 0.95rem;
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
                        <a href="<?= BASE_URL ?>public/consultar.php" class="btn btn-nav btn-nav-outline">
                            <i class="bi bi-search"></i> Consultar
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Form Container -->
        <div class="form-container">
            <div class="form-card">
                <div class="form-header">
                    <h4>
                        <i class="bi bi-file-earmark-plus"></i>
                        Enviar Justificativa de Falta
                    </h4>
                    <small>Preencha todos os campos obrigatórios (*)</small>
                </div>
                <div class="form-body">

                <?php if (!empty($erros)): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Corrija os erros abaixo:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach ($erros as $e): ?>
                                <li><?= htmlspecialchars($e) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (empty($alunos)): ?>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-circle"></i>
                        Nenhum aluno cadastrado no sistema. Contate a secretaria.
                    </div>
                <?php else: ?>

                <form method="POST" enctype="multipart/form-data" novalidate>

                    <!-- Dados do Aluno -->
                    <h6 class="section-title">
                        <i class="bi bi-person-badge"></i> Dados do Aluno
                    </h6>
                    <div class="row g-2 mb-2">
                        <div class="col-12">
                            <label class="form-label">Aluno *</label>
                            <select name="id_aluno" class="form-select" required>
                                <option value="">— Selecione o aluno —</option>
                                <?php foreach ($alunos as $a): ?>
                                    <option value="<?= $a['id_aluno'] ?>"
                                        <?= ($dados['id_aluno'] ?? '') == $a['id_aluno'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($a['nome']) ?> — Turma <?= htmlspecialchars($a['turma']) ?>
                                        (Mat. <?= htmlspecialchars($a['matricula']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Data da Falta *</label>
                            <input type="date" name="data_falta" class="form-control"
                                   max="<?= date('Y-m-d') ?>"
                                   value="<?= htmlspecialchars($dados['data_falta'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Motivo / Justificativa *</label>
                            <textarea name="motivo" class="form-control" rows="2"
                                      placeholder="Descreva o motivo da falta (ex: consulta médica, internação...)"
                                      required><?= htmlspecialchars($dados['motivo'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <hr class="section-divider">

                    <!-- Dados do Responsável -->
                    <h6 class="section-title">
                        <i class="bi bi-people"></i> Dados do Responsável
                    </h6>
                    <div class="row g-2 mb-2">
                        <div class="col-md-8">
                            <label class="form-label">Nome do Responsável *</label>
                            <input type="text" name="resp_nome" class="form-control"
                                   placeholder="Nome completo"
                                   value="<?= htmlspecialchars($dados['resp_nome'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">CPF *</label>
                            <input type="text" name="resp_cpf" id="cpf" class="form-control"
                                   placeholder="000.000.000-00" maxlength="14"
                                   value="<?php
                                       $cpf = $dados['resp_cpf'] ?? '';
                                       if (strlen($cpf) === 11) {
                                           $cpf = substr($cpf,0,3).'.'.substr($cpf,3,3).'.'.substr($cpf,6,3).'-'.substr($cpf,9,2);
                                       }
                                       echo htmlspecialchars($cpf);
                                   ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="resp_tel" class="form-control mask-tel"
                                   placeholder="(00) 00000-0000"
                                   value="<?= htmlspecialchars($dados['resp_tel'] ?? '') ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="resp_email" class="form-control"
                                   placeholder="email@exemplo.com"
                                   value="<?= htmlspecialchars($dados['resp_email'] ?? '') ?>">
                        </div>
                    </div>

                    <hr class="section-divider">

                    <!-- Anexo -->
                    <h6 class="section-title">
                        <i class="bi bi-paperclip"></i> Atestado / Documento
                    </h6>
                    <div class="mb-3">
                        <div class="upload-area" id="uploadArea">
                            <i class="bi bi-cloud-arrow-up d-block"></i>
                            <p id="fileLabel" class="mb-1 fw-bold">
                                Clique ou arraste o arquivo aqui
                            </p>
                            <small class="text-muted">PDF, JPG ou PNG — máximo 5 MB</small>
                            <input type="file" name="anexo" id="anexo" class="d-none"
                                   accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end flex-wrap">
                        <a href="<?= BASE_URL ?>public/index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                        <button type="submit" class="btn btn-primary-custom">
                            <i class="bi bi-send-fill me-2"></i>Enviar Protocolo
                        </button>
                    </div>
                </form>

                <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= BASE_URL ?>assets/js/main.js"></script>
</body>
</html>
