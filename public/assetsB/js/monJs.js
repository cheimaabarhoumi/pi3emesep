document.addEventListener('DOMContentLoaded', function() {
    // Initialisation du datepicker
    flatpickr('.datetimepicker', {
        enableTime: true,
        dateFormat: "d/m/Y H:i",
        minDate: "today",
        time_24hr: true,
        locale: "fr"
    });

    // Affichage dynamique du coût
    const entretienSelect = document.querySelector('#{{ form.entretien.vars.id }}');
    const coutDisplay = document.getElementById('cout-entretien');
    
    if (entretienSelect) {
        entretienSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value) {
                coutDisplay.textContent = `Coût estimé : ${selectedOption.dataset.cout} €`;
            } else {
                coutDisplay.textContent = '';
            }
        });
    }

    // Validation Bootstrap - seulement pour les champs non gérés par Symfony
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
});