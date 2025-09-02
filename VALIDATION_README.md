# Form Validation System - Neo Cars Project

## Overview

This project includes a comprehensive client-side form validation system that works with both front-end and back-end forms for the Neo Cars application. The validation system provides real-time feedback and prevents form submission until all required fields are properly filled.

## Features

- **Real-time validation**: Errors appear as you type or when fields lose focus
- **Form-specific validation**: Different validation rules for different form types
- **Visual feedback**: Clear error messages above input fields
- **Responsive design**: Works on both desktop and mobile devices
- **Automatic form detection**: Automatically attaches to forms when they're added to the page

## Form Types Supported

### 1. Admin Voiture Form (`admin-voiture-form`)
**Required fields:**
- Marque (Brand)
- Modèle (Model)
- Année (Year)
- Prix (Price)
- Puissance Fiscale (Fiscal Power)
- Kilométrage (Mileage)
- Boîte de Vitesse (Gearbox)
- Carburant (Fuel Type)
- Cylindrée (Engine Displacement)
- Nombre de Portes (Number of Doors)

**Validation rules:**
- Year: 1900 to current year + 1
- Price: Positive number, max 1,000,000
- Mileage: Positive number, max 1,000,000
- Fiscal Power: 1 to 100 CV
- Engine Displacement: 0.5L to 10L
- Number of Doors: 2 to 5
- Text fields: 2-50 characters for brand/model, 5-100 for title, 20-1000 for description

### 2. Admin Annonce Form (`admin-annonce-form`)
**Required fields:**
- Titre (Title)
- Voiture (Car)
- Description

**Validation rules:**
- Title: 5-100 characters
- Description: 20-1000 characters

### 3. Neo Form (Front-end forms)
**Required fields:** Same as admin voiture form
**Validation rules:** Same as admin voiture form

## How to Use

### 1. Include the Validation Script

Add this line to your Twig templates:

```twig
<script src="{{ asset('js/form-validation.js') }}"></script>
```

### 2. Structure Your Forms

Wrap each form field in a `form-group` div:

```twig
<div class="form-group">
    {{ form_label(form.field_name) }}
    {{ form_widget(form.field_name, {'attr': {'class': 'form-control', 'required': 'required'}}) }}
</div>
```

### 3. Add Required Attributes

For required fields, add the `required` attribute:

```twig
{{ form_widget(form.field_name, {'attr': {'class': 'form-control', 'required': 'required'}}) }}
```

### 4. Form IDs

Use these specific IDs for automatic validation:

- `admin-voiture-form` for admin voiture forms
- `admin-annonce-form` for admin annonce forms
- `neo-form` class for front-end forms

## CSS Classes

The validation system uses these CSS classes:

- `.is-invalid`: Applied to invalid fields
- `.validation-error-message`: Error message styling
- `.fv-error`: Additional error styling
- `.form-group`: Container for form fields

## Error Message Display

Error messages appear above the input fields and include:
- Red text color
- Exclamation icon
- Clear, descriptive text
- Smooth animation when appearing

## Testing

You can test the validation system using the demo page:

1. Navigate to `/public/validation-demo.html`
2. Try submitting empty forms
3. Enter invalid values
4. Test real-time validation

## Customization

### Adding New Validation Rules

Edit `assets/js/form-validation.js` and add new rules in the `validateField` function:

```javascript
if (/field_name/i.test(name)) {
    // Your validation logic here
    if (condition) {
        return 'Your error message';
    }
}
```

### Modifying Error Messages

Edit the validation function to change error message text:

```javascript
if (isRequired && !val) {
    return 'Your custom required message.';
}
```

### Styling

Modify `assets/styles/app.css` to change the appearance of error messages and invalid fields.

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Troubleshooting

### Validation Not Working

1. Check that the script is properly loaded
2. Verify form IDs match the expected values
3. Ensure form fields have proper `name` attributes
4. Check browser console for JavaScript errors

### Error Messages Not Displaying

1. Verify CSS is properly loaded
2. Check that form fields are wrapped in `.form-group` divs
3. Ensure proper positioning in CSS

### Forms in Modals

The validation system automatically detects dynamically added forms (like in modals) using a MutationObserver. No additional setup is required.

## Support

For issues or questions about the validation system, check:
1. Browser console for JavaScript errors
2. Network tab for missing assets
3. Form structure and naming conventions
4. CSS conflicts with existing styles

