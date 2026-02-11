// ============================================================================
// General Functions
// ============================================================================

function validateForm() {
    let valid = true;
    const errorClass = 'error';

    // Clear existing errors
    document.querySelectorAll(`.${errorClass}`).forEach(el => el.remove());

    // Validate fields
    const name = document.querySelector('input[name="name_on_card"]');
    if (!name.value.trim()) {
        showError(name, 'Name on card is required.');
        valid = false;
    }

    const cardNumber = document.querySelector('input[name="card_number"]');
    if (!/^\d{4}-\d{4}-\d{4}-\d{4}$/.test(cardNumber.value)) {
        showError(cardNumber, 'Please enter a valid card number (format: ****-****-****-****).');
        valid = false;
    }

    const expMonth = document.querySelector('input[name="exp_month"]');
    if (!expMonth.value.trim()) {
        showError(expMonth, 'Expiration month is required.');
        valid = false;
    }

    const expYear = document.querySelector('input[name="exp_year"]');
    if (!/^\d{4}$/.test(expYear.value)) {
        showError(expYear, 'Please enter a valid 4-digit expiration year.');
        valid = false;
    }

    const cvv = document.querySelector('input[name="cvv"]');
    if (!/^\d{3}$/.test(cvv.value)) {
        showError(cvv, 'Please enter a valid 3-digit CVV.');
        valid = false;
    }

    return valid;
}

function showError(input, message) {
    const error = document.createElement('div');
    error.classList.add('error');
    error.textContent = message;
    input.parentElement.appendChild(error);
}

// ============================================================================
// Page Load (jQuery)
// ============================================================================

$(() => {

    // Autofocus
    $('form :input:not(button):first').focus();
    $('.err:first').prev().focus();
    $('.err:first').prev().find(':input:first').focus();

    // Initiate GET request
    $('[data-get]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.get;
        location = url || location;
    });

    // Initiate POST request
    $('[data-post]').on('click', e => {
        e.preventDefault();
        const url = e.target.dataset.post;
        const f = $('<form>').appendTo(document.body)[0];
        f.method = 'POST';
        f.action = url || location;
        f.submit();
    });

    // Reset form
    $('[type=reset]').on('click', e => {
        e.preventDefault();
        location = location;
    });
});