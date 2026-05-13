<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$perfil = $_SESSION['usuario_perfil'] ?? '';
?>
<nav class="sidebar d-flex flex-column">
    <div class="brand">
        <i class="bi bi-file-earmark-check-fill text-warning fs-4"></i>
        <h5 class="mt-2">Protocolo Eletrônico</h5>
        <small class="text-white-50">Justificativas de Faltas</small>
    </div>

    <ul class="nav flex-column mt-3 flex-grow-1">
        <li class="nav-item">
            <a href="<?= BASE_URL ?>admin/dashboard.php"
               class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>admin/protocolos.php"
               class="nav-link <?= $currentPage === 'protocolos.php' ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-text"></i> Protocolos
            </a>
        </li>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>admin/alunos.php"
               class="nav-link <?= $currentPage === 'alunos.php' ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Alunos
            </a>
        </li>
        <?php if ($perfil === 'ADMINISTRADOR'): ?>
        <li class="nav-item">
            <a href="<?= BASE_URL ?>admin/usuarios.php"
               class="nav-link <?= $currentPage === 'usuarios.php' ? 'active' : '' ?>">
                <i class="bi bi-person-gear"></i> Usuários
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <div class="p-3 border-top border-secondary">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-person-circle text-white fs-5"></i>
            <div>
                <div class="text-white small fw-bold"><?= htmlspecialchars($_SESSION['usuario_nome'] ?? '') ?></div>
                <div class="text-white-50" style="font-size:.75rem"><?= htmlspecialchars($perfil) ?></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>admin/logout.php" class="nav-link text-danger">
            <i class="bi bi-box-arrow-left"></i> Sair
        </a>
    </div>
</nav>
