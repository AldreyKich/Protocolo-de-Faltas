<?php
define('BASE_URL', '/protocolo_faltas/');
require_once __DIR__ . '/../config/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Protocolo Eletrônico de Justificativas</title>
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
            --muted: #64748b;
            --brand: #3b82f6;
            --brand-dark: #2563eb;
            --accent: #10b981;
            --accent-dark: #059669;
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
            animation-delay: 0s;
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
            display: flex;
            flex-direction: column;
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
            font-size: 1.5rem;
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
        }

        .btn-nav-solid {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            border: none;
        }

        .btn-nav-solid:hover {
            background: linear-gradient(135deg, var(--brand-dark) 0%, var(--brand) 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        /* Hero Section */
        .hero-section {
            padding: 1.5rem 0 1rem;
            text-align: center;
        }

        .hero-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem 2rem 1.8rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
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

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.4rem 1rem;
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 50px;
            color: var(--accent);
            font-size: 0.85rem;
            font-weight: 700;
            margin-bottom: 1rem;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .hero-title {
            font-family: "Space Grotesk", -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            font-weight: 800;
            color: #fff;
            margin-bottom: 0.8rem;
            line-height: 1.2;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
            letter-spacing: -0.02em;
        }

        .hero-subtitle {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            max-width: 650px;
            margin: 0 auto 1.5rem;
            line-height: 1.6;
        }

        .hero-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            color: #fff;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }

        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(59, 130, 246, 0.6);
            color: #fff;
        }

        .btn-hero-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .btn-hero-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateY(-3px);
            color: #fff;
        }

        /* Features Section */
        .features-section {
            padding: 1.5rem 0 2rem;
            background: linear-gradient(180deg, transparent 0%, rgba(255, 255, 255, 0.03) 100%);
        }

        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 800;
            color: #fff;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 1.5rem;
            height: 100%;
            transition: all 0.4s ease;
            animation: fadeInUp 0.8s ease;
            animation-fill-mode: both;
        }

        .feature-card:nth-child(1) { animation-delay: 0.1s; }
        .feature-card:nth-child(2) { animation-delay: 0.2s; }
        .feature-card:nth-child(3) { animation-delay: 0.3s; }

        .feature-card:hover {
            transform: translateY(-10px);
            background: rgba(255, 255, 255, 0.12);
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 20px 40px rgba(59, 130, 246, 0.3);
        }

        .feature-icon {
            width: 55px;
            height: 55px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-dark) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
            margin-bottom: 1rem;
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.4);
        }

        .feature-number {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: rgba(16, 185, 129, 0.2);
            border: 2px solid var(--accent);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .feature-card {
            position: relative;
        }

        .feature-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 0.6rem;
        }

        .feature-description {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            line-height: 1.5;
        }

        /* Footer */
        .site-footer {
            background: rgba(15, 23, 42, 0.9);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1rem 0;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            margin-top: auto;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-container {
                padding: 2rem 1.5rem;
            }

            .hero-title {
                font-size: 2rem;
            }

            .hero-subtitle {
                font-size: 1rem;
            }

            .btn-hero {
                width: 100%;
                justify-content: center;
            }

            .section-title {
                font-size: 2rem;
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
                    <div class="d-flex gap-3">
                        <a href="<?= BASE_URL ?>public/consultar.php" class="btn btn-nav btn-nav-outline">
                            <i class="bi bi-search"></i> Consultar
                        </a>
                        <a href="<?= BASE_URL ?>admin/login.php" class="btn btn-nav btn-nav-solid">
                            <i class="bi bi-lock"></i> Admin
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="hero-section">
            <div class="container">
                <div class="hero-container">
                    <span class="hero-badge">
                        <i class="bi bi-shield-check"></i>
                        Sistema Oficial e Seguro
                    </span>
                    <h1 class="hero-title">
                        Justifique suas Faltas de Forma Digital
                    </h1>
                    <p class="hero-subtitle">
                        Envie atestados médicos, acompanhe o status em tempo real e tenha total controle sobre suas solicitações. Rápido, seguro e sem burocracia.
                    </p>
                    <div class="hero-actions">
                        <a href="<?= BASE_URL ?>public/novo_protocolo.php" class="btn-hero btn-hero-primary">
                            <i class="bi bi-plus-circle-fill"></i>
                            Nova Justificativa
                        </a>
                        <a href="<?= BASE_URL ?>public/consultar.php" class="btn-hero btn-hero-secondary">
                            <i class="bi bi-search"></i>
                            Consultar Protocolo
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <div class="container">
                <h2 class="section-title">Como Funciona</h2>
                <div class="row g-4">
                    <div class="col-md-4">
                        <article class="feature-card">
                            <span class="feature-number">1</span>
                            <div class="feature-icon">
                                <i class="bi bi-laptop"></i>
                            </div>
                            <h3 class="feature-title">Acesse Online</h3>
                            <p class="feature-description">
                                Entre no portal de qualquer lugar, a qualquer hora. Computador, tablet ou celular.
                            </p>
                        </article>
                    </div>
                    <div class="col-md-4">
                        <article class="feature-card">
                            <span class="feature-number">2</span>
                            <div class="feature-icon">
                                <i class="bi bi-file-earmark-arrow-up"></i>
                            </div>
                            <h3 class="feature-title">Envie Documentos</h3>
                            <p class="feature-description">
                                Preencha o formulário e anexe seu atestado em PDF ou imagem. Processo simples e rápido.
                            </p>
                        </article>
                    </div>
                    <div class="col-md-4">
                        <article class="feature-card">
                            <span class="feature-number">3</span>
                            <div class="feature-icon">
                                <i class="bi bi-check2-circle"></i>
                            </div>
                            <h3 class="feature-title">Acompanhe Status</h3>
                            <p class="feature-description">
                                Consulte o andamento usando seu número de protocolo. Transparência total no processo.
                            </p>
                        </article>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <p class="mb-0">
                <i class="bi bi-shield-check me-2"></i>
                Protocolo Eletrônico de Justificativas &copy; <?= date('Y') ?> - Todos os direitos reservados
            </p>
        </div>
    </footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
