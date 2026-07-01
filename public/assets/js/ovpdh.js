/**
 * OVPDH — Observatório da Violência Policial e Direitos Humanos
 * Scripts complementares e interativos do sistema
 */

document.addEventListener("DOMContentLoaded", function() {
    // Alertas que desaparecem após alguns segundos
    const alerts = document.querySelectorAll('.alert-ovpdh-success, .alert-ovpdh-error');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // Checkbox de Anônimo na tela de vítimas
    const anonimoCheckbox = document.getElementById('anonimo');
    const nomeInput = document.getElementById('nome');
    if (anonimoCheckbox && nomeInput) {
        anonimoCheckbox.addEventListener('change', function() {
            if (this.checked) {
                nomeInput.value = '';
                nomeInput.disabled = true;
                nomeInput.placeholder = 'Mantido sob anonimato';
            } else {
                nomeInput.disabled = false;
                nomeInput.placeholder = 'Insira o nome (se conhecido)';
            }
        });
    }
});
