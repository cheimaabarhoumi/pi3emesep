// Generic form validation for Symfony forms (client-side)
// Attach to all forms on the page and validate common fields used in the project.
(function () {
    const KB = 1024;
    const MB = KB * KB;

    const requiredNames = new Set(['titre', 'description', 'marque', 'modele', 'annee', 'prix']);

    function extractSimpleName(fullName) {
        // handle names like annonce[titre] or voiture[marque]
        if (!fullName) return '';
        const m = fullName.match(/\[([^\]]+)\]$/);
        if (m) return m[1];
        // fallback: return last part after dot or hyphen
        const parts = fullName.split(/\.|-/);
        return parts[parts.length - 1];
    }

    function clearError(field) {
        if (!field) return;
        // remove invalid class from the field itself or its nearest .form-control
        if (field.classList) field.classList.remove('is-invalid');
        const control = field.closest && field.closest('.form-control') ? field.closest('.form-control') : field;
        if (control && control.classList) control.classList.remove('is-invalid');

        // remove any feedback elements we added (invalid-feedback or fv-error)
        const next = field.nextElementSibling;
        if (next && next.classList && (next.classList.contains('invalid-feedback') || next.classList.contains('fv-error'))) {
            next.remove();
        }
        // also try to remove feedback inside input-group wrappers
        const wrapper = field.parentElement;
        if (wrapper) {
            const existing = wrapper.querySelector('.fv-error');
            if (existing) existing.remove();
        }
    }

    function showError(field, message) {
        if (!field) return;
        clearError(field);

        // prefer to add is-invalid to nearest input/select/textarea or wrapper with class form-control
        const control = field.closest && field.closest('.form-control') ? field.closest('.form-control') : field;
        if (control && control.classList) control.classList.add('is-invalid');

        // create feedback element styled for Soft UI Dashboard (Bootstrap-based)
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback fv-error text-sm text-danger font-weight-bold';
        feedback.style.display = 'block';
        feedback.textContent = message;

        // Insert feedback after the field, or inside the parent wrapper if present
        const next = field.nextElementSibling;
        if (next) {
            // if next is a help text or similar, place after it
            if (next.classList && (next.classList.contains('form-text') || next.classList.contains('input-group-text'))) {
                next.insertAdjacentElement('afterend', feedback);
            } else {
                field.insertAdjacentElement('afterend', feedback);
            }
        } else if (field.parentElement) {
            field.parentElement.appendChild(feedback);
        } else {
            field.insertAdjacentElement('afterend', feedback);
        }
    }

    function validateField(field) {
        const name = extractSimpleName(field.name || '');
        const type = field.type || field.tagName.toLowerCase();
        const val = (field.value || '').trim();

        // empty file input is valid if not required
        if (type === 'file') {
            // only validate if file provided
            if (field.files && field.files.length > 0) {
                const f = field.files[0];
                if (name === 'image') {
                    const allowed = ['image/jpeg', 'image/png'];
                    if (allowed.indexOf(f.type) === -1) {
                        return 'Veuillez uploader une image JPEG ou PNG.';
                    }
                    if (f.size > 2 * MB) return 'L\'image doit faire moins de 2MB.';
                }
            }
            return true;
        }

        // required checks
        const isRequired = field.hasAttribute('required') || requiredNames.has(name);
        if (isRequired && !val) return 'Ce champ est obligatoire.';

        if (!val) return true; // not required and empty

        if (/annee/i.test(name)) {
            const y = parseInt(val, 10);
            const min = 1900;
            const max = new Date().getFullYear();
            if (Number.isNaN(y)) return 'L\'année doit être un nombre.';
            if (y < min || y > max) return `L\'année doit être comprise entre ${min} et ${max}.`;
        }

        if (/prix|price|prix_total/i.test(name)) {
            const n = parseFloat(val.replace(',', '.'));
            if (Number.isNaN(n)) return 'Le prix doit être un nombre.';
            if (n <= 0) return 'Le prix doit être un nombre positif.';
        }

        if (/titre|description|marque|modele/i.test(name)) {
            // reasonable length checks
            if (val.length < 2) return 'Ce champ est trop court.';
            if (val.length > 255) return 'Ce champ est trop long.';
        }

        return true;
    }

    function attachForm(form) {
        if (!form) return;
        form.addEventListener('submit', function (e) {
            const fields = Array.from(form.querySelectorAll('input, textarea, select')).filter(f => f.name);
            let valid = true;
            for (const f of fields) {
                clearError(f);
                const r = validateField(f);
                if (r !== true) {
                    valid = false;
                    showError(f, r);
                    // focus first invalid
                    if (valid === false) {
                        try { f.focus(); } catch (err) {}
                    }
                }
            }
            if (!valid) {
                e.preventDefault();
                e.stopPropagation();
            }
        }, { passive: false });

        // clear on input/change
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(i => {
            i.addEventListener('input', () => clearError(i));
            i.addEventListener('change', () => clearError(i));
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const forms = Array.from(document.querySelectorAll('form'));
        forms.forEach(attachForm);
    });
})();
