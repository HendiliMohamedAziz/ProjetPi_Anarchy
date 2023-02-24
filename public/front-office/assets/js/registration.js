const isCoachField = document.getElementById('registration_form_isCoach');
const coachFields = document.getElementById('coach-fields');

isCoachField.addEventListener('change', () => {
    if (isCoachField.checked) {
        coachFields.style.display = 'block';
    } else {
        coachFields.style.display = 'none';
    }
});