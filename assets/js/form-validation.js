// Enhanced form validation for Neo Cars project
// Works with both front and back forms for annonce and voiture
export default function initFormValidation() {
    const KB = 1024;
    const MB = KB * KB;

    // Define required fields for different form types
    const formRequirements = {
        'admin-voiture-form': ['marque', 'modele', 'annee', 'prix', 'puissance_fiscale', 'kilometrage', 'boite_vitesse', 'carburant', 'cylindree', 'nombre_portes'],
        'admin-annonce-form': ['titre', 'voiture', 'description'],
        'neo-form': ['marque', 'modele', 'annee', 'prix', 'puissance_fiscale', 'kilometrage', 'boite_vitesse', 'carburant', 'cylindree', 'nombre_portes'],
        'default': ['titre', 'description', 'marque', 'modele', 'annee', 'prix']
    };

    function extractSimpleName(fullName) {
        if (!fullName) return '';
        const m = fullName.match(/\[([^\]]+)\]$/);
        if (m) return m[1];
        const parts = fullName.split(/\.|-/);
        return parts[parts.length - 1];
    }

    function getFormType(form) {
        if (form.id) {
            return form.id;
        }
        if (form.classList.contains('neo-form')) {
            return 'neo-form';
        }
        return 'default';
    }

    function getRequiredFields(form) {
        const formType = getFormType(form);
        return formRequirements[formType] || formRequirements['default'];
    }

    function clearError(field) {
        if (!field) return;
        
        // Remove invalid classes
        field.classList.remove('is-invalid');
        const control = field.closest('.form-control') || field.closest('.neo-form-control');
        if (control) {
            control.classList.remove('is-invalid');
        }

        // Remove existing error messages
        const existingError = field.parentElement.querySelector('.validation-error-message');
        if (existingError) {
            existingError.remove();
        }

        // Remove any fv-error elements
        const fvError = field.parentElement.querySelector('.fv-error');
        if (fvError) {
            fvError.remove();
        }
    }

    function showError(field, message) {
        if (!field) return;
        
        console.log('Showing error for field:', field.name, 'Message:', message);
        
        clearError(field);

        // Add invalid class to field
        field.classList.add('is-invalid');
        const control = field.closest('.form-control') || field.closest('.neo-form-control');
        if (control) {
            control.classList.add('is-invalid');
        }

        // Create error message element
        const errorDiv = document.createElement('div');
        errorDiv.className = 'validation-error-message text-danger small mt-1 fv-error';
        errorDiv.style.display = 'block';
        errorDiv.innerHTML = `<i class="fas fa-exclamation-circle me-1"></i>${message}`;

        // Insert error message above the field
        const fieldContainer = field.parentElement;
        if (fieldContainer) {
            fieldContainer.insertBefore(errorDiv, field);
            console.log('Error message inserted above field');
        } else {
            console.log('No field container found');
        }
    }

    function validateField(field, requiredFields) {
        const name = extractSimpleName(field.name || '');
        const type = field.type || field.tagName.toLowerCase();
        const val = (field.value || '').trim();

        // Check if field is required
        const isRequired = field.hasAttribute('required') || requiredFields.includes(name);
        
        // File validation
        if (type === 'file') {
            if (field.files && field.files.length > 0) {
                const file = field.files[0];
                if (name === 'image') {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                    if (!allowedTypes.includes(file.type)) {
                        return 'Veuillez sélectionner une image au format JPEG ou PNG.';
                    }
                    if (file.size > 10 * MB) {
                        return 'L\'image doit faire moins de 10MB.';
                    }
                }
            } else if (isRequired) {
                return 'Veuillez sélectionner un fichier.';
            }
            return true;
        }

        // Required field validation
        if (isRequired && !val) {
            return 'Ce champ est obligatoire.';
        }

        if (!val) return true; // Not required and empty

        // Specific field validations
        if (/annee/i.test(name)) {
            const year = parseInt(val, 10);
            const minYear = 1900;
            const maxYear = new Date().getFullYear() + 1;
            if (Number.isNaN(year)) {
                return 'L\'année doit être un nombre valide.';
            }
            if (year < minYear || year > maxYear) {
                return `L'année doit être comprise entre ${minYear} et ${maxYear}.`;
            }
        }

        if (/prix|price/i.test(name)) {
            const price = parseFloat(val.replace(/[,\s]/g, '.'));
            if (Number.isNaN(price)) {
                return 'Le prix doit être un nombre valide.';
            }
            if (price <= 0) {
                return 'Le prix doit être un nombre positif.';
            }
            if (price > 1000000) {
                return 'Le prix semble trop élevé.';
            }
        }

        if (/kilometrage/i.test(name)) {
            const km = parseInt(val.replace(/\s/g, ''), 10);
            if (Number.isNaN(km)) {
                return 'Le kilométrage doit être un nombre valide.';
            }
            if (km < 0) {
                return 'Le kilométrage ne peut pas être négatif.';
            }
            if (km > 1000000) {
                return 'Le kilométrage semble trop élevé.';
            }
        }

        if (/puissance_fiscale/i.test(name)) {
            const power = parseInt(val, 10);
            if (Number.isNaN(power)) {
                return 'La puissance fiscale doit être un nombre valide.';
            }
            if (power < 1 || power > 100) {
                return 'La puissance fiscale doit être comprise entre 1 et 100 CV.';
            }
        }

        if (/cylindree/i.test(name)) {
            const cylinder = parseFloat(val.replace(',', '.'));
            if (Number.isNaN(cylinder)) {
                return 'La cylindrée doit être un nombre valide.';
            }
            if (cylinder < 0.5 || cylinder > 10) {
                return 'La cylindrée doit être comprise entre 0.5L et 10L.';
            }
        }

        if (/nombre_portes/i.test(name)) {
            const doors = parseInt(val, 10);
            if (Number.isNaN(doors)) {
                return 'Le nombre de portes doit être un nombre valide.';
            }
            if (doors < 2 || doors > 5) {
                return 'Le nombre de portes doit être compris entre 2 et 5.';
            }
        }

        if (/titre/i.test(name)) {
            if (val.length < 5) {
                return 'Le titre doit contenir au moins 5 caractères.';
            }
            if (val.length > 100) {
                return 'Le titre ne peut pas dépasser 100 caractères.';
            }
        }

        if (/description/i.test(name)) {
            if (val.length < 20) {
                return 'La description doit contenir au moins 20 caractères.';
            }
            if (val.length > 1000) {
                return 'La description ne peut pas dépasser 1000 caractères.';
            }
        }

        if (/marque|modele/i.test(name)) {
            if (val.length < 2) {
                return 'Ce champ doit contenir au moins 2 caractères.';
            }
            if (val.length > 50) {
                return 'Ce champ ne peut pas dépasser 50 caractères.';
            }
        }

        return true;
    }

    function attachForm(form) {
        if (!form) return;
        
        console.log('Attaching validation to form:', form.id || 'unnamed');

        const requiredFields = getRequiredFields(form);

        // Handle form submission
        form.addEventListener('submit', function (e) {
            console.log('Form submission attempted');
            const fields = Array.from(form.querySelectorAll('input, textarea, select')).filter(f => f.name);
            console.log('Fields to validate:', fields.length);
            let isValid = true;
            let firstInvalidField = null;

            // Clear all previous errors
            fields.forEach(field => clearError(field));

            // Validate each field
            for (const field of fields) {
                const validationResult = validateField(field, requiredFields);
                console.log('Field validation:', field.name, validationResult);
                if (validationResult !== true) {
                    isValid = false;
                    showError(field, validationResult);
                    if (!firstInvalidField) {
                        firstInvalidField = field;
                    }
                }
            }

            if (!isValid) {
                console.log('Form validation failed, preventing submission');
                e.preventDefault();
                e.stopPropagation();
                
                // Focus first invalid field
                if (firstInvalidField) {
                    firstInvalidField.focus();
                    firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                console.log('Form validation passed');
            }
        }, { passive: false });

        // Real-time validation on input/change
        const fields = form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            field.addEventListener('input', () => {
                if (field.value.trim()) {
                    clearError(field);
                    const validationResult = validateField(field, requiredFields);
                    if (validationResult !== true) {
                        showError(field, validationResult);
                    }
                } else {
                    clearError(field);
                }
            });

            field.addEventListener('blur', () => {
                const validationResult = validateField(field, requiredFields);
                if (validationResult !== true) {
                    showError(field, validationResult);
                }
            });

            field.addEventListener('focus', () => {
                clearError(field);
            });
        });
    }

    // Initialize validation when DOM is ready
    document.addEventListener('DOMContentLoaded', function () {
        console.log('Form validation script loaded');
        const forms = Array.from(document.querySelectorAll('form'));
        console.log('Found forms:', forms.length);
        forms.forEach(attachForm);

        // Also handle dynamically added forms (for modals)
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && node.tagName === 'FORM') {
                        console.log('New form detected:', node);
                        attachForm(node);
                    } else if (node.nodeType === 1 && node.querySelectorAll) {
                        const newForms = node.querySelectorAll('form');
                        if (newForms.length > 0) {
                            console.log('New forms in node:', newForms.length);
                            newForms.forEach(attachForm);
                        }
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
}
    