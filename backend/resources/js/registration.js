export function isValidPhone(value) {
    const normalized = value.replace(/[\s()-]/g, '');
    return /^\+[1-9]\d{7,14}$/.test(normalized);
}

export function setupRegistration(enterDemo) {
    const dialog = document.getElementById('registration-dialog');
    const form = document.getElementById('registration-form');
    const personal = document.getElementById('registration-personal');
    const security = document.getElementById('registration-security');
    const name = document.getElementById('register-name');
    const email = document.getElementById('register-email');
    const phone = document.getElementById('register-phone');
    const password = document.getElementById('register-password');
    const progress = document.getElementById('registration-progress');
    form.noValidate = true;
    let step = 1;
    const method = () => form.querySelector('[name="contact-method"]:checked').value;
    function fieldError(input, id, message) {
        const error = document.getElementById(id);
        error.textContent = message; error.hidden = !message;
        input.setAttribute('aria-invalid', String(Boolean(message)));
    }
    function updateMethod() {
        const useEmail = method() === 'email';
        document.getElementById('register-email-group').hidden = !useEmail;
        document.getElementById('register-phone-group').hidden = useEmail;
        email.disabled = !useEmail; email.required = useEmail;
        phone.disabled = useEmail; phone.required = !useEmail;
        document.getElementById('contact-hint').textContent = useEmail ? 'Usaremos este correo para verificar tu cuenta cuando el servicio esté disponible.' : 'Incluye el código de país, por ejemplo +51 o +57. La verificación por SMS estará disponible al conectar el servicio.';
        fieldError(email, 'contact-error', ''); phone.setAttribute('aria-invalid', 'false');
    }
    function setStep(next) {
        step = next;
        personal.hidden = next !== 1; personal.disabled = next !== 1;
        security.hidden = next !== 2; security.disabled = next !== 2;
        progress.setAttribute('aria-label', `Paso ${next} de 2`);
        progress.firstElementChild.classList.toggle('current', next === 1);
        document.getElementById('security-step').classList.toggle('current', next === 2);
        document.getElementById('registration-description').textContent = next === 1 ? 'Crea tu perfil con correo o teléfono. Tú eliges cómo conectar.' : 'Solo falta una contraseña para completar tu perfil.';
        if (next === 2) {
            document.getElementById('registration-summary-name').textContent = name.value.trim();
            document.getElementById('registration-summary-contact').textContent = method() === 'email' ? email.value.trim() : phone.value.trim();
        }
    }
    function validatePersonal() {
        const nameValid = name.value.trim().length >= 2;
        fieldError(name, 'name-error', nameValid ? '' : 'Escribe tu nombre, con al menos 2 caracteres.');
        const contact = method() === 'email' ? email : phone;
        const contactValid = method() === 'email' ? Boolean(email.value.trim()) && email.validity.valid : isValidPhone(phone.value.trim());
        fieldError(contact, 'contact-error', contactValid ? '' : method() === 'email' ? 'Revisa el correo. Debe tener un formato como nombre@ejemplo.com.' : 'Incluye +, el código de país y tu número (de 8 a 15 dígitos).');
        if (!nameValid) name.focus(); else if (!contactValid) contact.focus();
        return nameValid && contactValid;
    }
    function nextStep() { if (validatePersonal()) { setStep(2); password.focus(); } }
    function updateStrength() {
        const length = Array.from(password.value).length;
        const score = length === 0 ? 0 : length < 10 ? 1 : length < 14 ? 2 : length < 20 ? 3 : 4;
        const fill = document.getElementById('password-meter-fill');
        fill.style.width = `${score * 25}%`;
        fill.style.background = ['#b1c2b5', '#c28b66', '#b49a53', '#70a880', '#2f8870'][score];
        document.getElementById('password-strength').textContent = ['Escribe una contraseña para ver su fortaleza.', 'Muy corta · Añade más caracteres.', 'Longitud mínima alcanzada · Una frase más larga es mejor.', 'Buena longitud · Sigue así.', 'Excelente longitud · Evita frases comunes o datos personales.'][score];
        fieldError(password, 'password-error', '');
    }
    function openRegistration() {
        form.reset(); password.type = 'password';
        document.getElementById('toggle-register-password').setAttribute('aria-pressed', 'false');
        document.getElementById('toggle-register-password').setAttribute('aria-label', 'Mostrar contraseña de registro');
        form.hidden = false; progress.hidden = false;
        document.getElementById('registration-success').hidden = true;
        document.getElementById('registration-existing').hidden = false;
        document.getElementById('registration-title').innerHTML = 'Tu próxima aventura<br>empieza aquí<span>.</span>';
        fieldError(name, 'name-error', ''); fieldError(email, 'contact-error', ''); updateMethod(); updateStrength(); setStep(1);
        dialog.showModal(); name.focus();
    }
    document.getElementById('open-registration').addEventListener('click', openRegistration);
    document.getElementById('close-registration').addEventListener('click', () => dialog.close());
    for (const id of ['registration-signin', 'registration-login']) document.getElementById(id).addEventListener('click', () => dialog.close());
    document.getElementById('registration-next').addEventListener('click', nextStep);
    document.getElementById('registration-back').addEventListener('click', () => { setStep(1); name.focus(); });
    document.getElementById('registration-explore').addEventListener('click', enterDemo);
    form.querySelectorAll('[name="contact-method"]').forEach(radio => radio.addEventListener('change', updateMethod));
    name.addEventListener('input', () => fieldError(name, 'name-error', ''));
    for (const contact of [email, phone]) contact.addEventListener('input', () => fieldError(contact, 'contact-error', ''));
    password.addEventListener('input', updateStrength);
    document.getElementById('toggle-register-password').addEventListener('click', event => {
        const show = password.type === 'password'; password.type = show ? 'text' : 'password';
        event.currentTarget.setAttribute('aria-pressed', String(show));
        event.currentTarget.setAttribute('aria-label', show ? 'Ocultar contraseña de registro' : 'Mostrar contraseña de registro');
    });
    form.addEventListener('submit', event => {
        event.preventDefault();
        if (step === 1) { nextStep(); return; }
        if (Array.from(password.value).length < 10 || !password.value.trim()) {
            fieldError(password, 'password-error', 'Usa al menos 10 caracteres. Una frase larga y fácil de recordar funciona bien.'); password.focus(); return;
        }
        form.reset(); password.value = ''; name.value = ''; email.value = ''; phone.value = '';
        form.hidden = true; progress.hidden = true;
        document.getElementById('registration-existing').hidden = true;
        document.getElementById('registration-title').textContent = 'Tu viaje está por comenzar.';
        document.getElementById('registration-description').textContent = 'Gracias por probar esta nueva experiencia.';
        document.getElementById('registration-success').hidden = false;
        document.getElementById('registration-success').focus();
    });
    dialog.addEventListener('close', () => { form.reset(); password.value = ''; });
}
