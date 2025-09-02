// public/js/form-validator.js
(function(){
  function showError(input, message) {
    var errorElem = input.previousElementSibling;
    if (!errorElem || !errorElem.classList.contains('form-error')) {
      errorElem = document.createElement('div');
      errorElem.className = 'form-error text-danger mb-1 text-sm';
      input.insertAdjacentElement('beforebegin', errorElem);
    }
    errorElem.textContent = message;
  }

  function clearError(input) {
    var next = input.nextElementSibling;
    if (next && next.classList.contains('form-error')) {
      next.remove();
    }
    var prev = input.previousElementSibling;
    if (prev && prev.classList.contains('form-error')) {
      prev.remove();
    }
  }

  window.initFormValidation = function(formSelector, rulesConfig) {
    var form = document.querySelector(formSelector);
    if (!form) return;
    form.addEventListener('submit', function(e) {
      var valid = true;
      Object.keys(rulesConfig).forEach(function(fieldName) {
        var rules = rulesConfig[fieldName];
        var input = form.querySelector('[name="' + fieldName + '"]')
                    || form.querySelector('[name$="[' + fieldName + ']"]');
        if (!input) return;
        clearError(input);
        if (rules.required && !input.value.trim()) {
          showError(input, (rules.messages && rules.messages.required) || 'This field is required');
          valid = false;
          return;
        }
        if (rules.minLength && input.value.trim().length < rules.minLength) {
          showError(input, (rules.messages && rules.messages.minLength) || 'Must be at least ' + rules.minLength + ' characters');
          valid = false;
        }
        if (rules.maxLength && input.value.trim().length > rules.maxLength) {
          showError(input, (rules.messages && rules.messages.maxLength) || 'Must be at most ' + rules.maxLength + ' characters');
          valid = false;
        }
        if (rules.min != null && parseFloat(input.value) < rules.min) {
          showError(input, (rules.messages && rules.messages.min) || 'Must be at least ' + rules.min);
          valid = false;
        }
        if (rules.max != null && parseFloat(input.value) > rules.max) {
          showError(input, (rules.messages && rules.messages.max) || 'Must be at most ' + rules.max);
          valid = false;
        }
        if (rules.pattern && !(new RegExp(rules.pattern)).test(input.value)) {
          showError(input, (rules.messages && rules.messages.pattern) || 'Invalid format');
          valid = false;
        }
      });
      if (!valid) e.preventDefault();
    });
  };
})();