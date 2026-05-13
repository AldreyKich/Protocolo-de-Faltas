/* =============================================
   PROTOCOLO ELETRÔNICO - Scripts Globais
   ============================================= */

document.addEventListener('DOMContentLoaded', function () {

    // ---- Upload drag & drop ----
    const uploadArea = document.getElementById('uploadArea');
    const fileInput  = document.getElementById('anexo');
    const fileLabel  = document.getElementById('fileLabel');

    if (uploadArea && fileInput) {
        uploadArea.addEventListener('click', () => fileInput.click());

        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                updateFileLabel(e.dataTransfer.files[0].name);
            }
        });

        fileInput.addEventListener('change', () => {
            if (fileInput.files.length) updateFileLabel(fileInput.files[0].name);
        });

        function updateFileLabel(name) {
            if (fileLabel) fileLabel.textContent = '📎 ' + name;
        }
    }

    // ---- Máscara CPF ----
    const cpfInput = document.getElementById('cpf');
    if (cpfInput) {
        cpfInput.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 11);
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d)/, '$1.$2');
            v = v.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            this.value = v;
        });
    }

    // ---- Máscara Telefone ----
    const telInputs = document.querySelectorAll('.mask-tel');
    telInputs.forEach(function (el) {
        el.addEventListener('input', function () {
            let v = this.value.replace(/\D/g, '').substring(0, 11);
            if (v.length <= 10) {
                v = v.replace(/(\d{2})(\d)/, '($1) $2');
                v = v.replace(/(\d{4})(\d)/, '$1-$2');
            } else {
                v = v.replace(/(\d{2})(\d)/, '($1) $2');
                v = v.replace(/(\d{5})(\d)/, '$1-$2');
            }
            this.value = v;
        });
    });

    // ---- Sidebar toggle (mobile) ----
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('open'));
    }

    // ---- Auto-dismiss alerts ----
    const alerts = document.querySelectorAll('.alert-auto');
    alerts.forEach(function (alert) {
        setTimeout(() => {
            alert.style.transition = 'opacity .5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 4000);
    });

    // ---- Confirmação de exclusão ----
    document.querySelectorAll('.btn-delete-confirm').forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            if (!confirm('Tem certeza que deseja excluir este registro?')) {
                e.preventDefault();
            }
        });
    });

    // ---- Tooltip Bootstrap ----
    const tooltipEls = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltipEls.forEach(el => new bootstrap.Tooltip(el));
});
