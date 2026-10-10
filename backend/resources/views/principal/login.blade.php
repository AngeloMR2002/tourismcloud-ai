<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Accede a tu espacio de gestión turística en TourismCloud AI.">
    <title>Iniciar sesión · TourismCloud AI</title>
    @vite(['resources/css/app.css', 'resources/css/principal.css', 'resources/js/principal.js'])
</head>
<body class="login-page" data-page="login">
    <main class="login-shell">
        <section class="login-art" aria-label="TourismCloud AI, turismo conectado">
            <a class="brand brand-light" href="{{ route('catalogo.atractivos.index') }}"><span class="brand-symbol">◎</span> TourismCloud<span class="brand-ai">AI</span></a>
            <div class="art-grid" aria-hidden="true"></div>
            <div class="art-copy"><span class="eyebrow"><i></i> EL SIGUIENTE DESTINO EMPIEZA AQUÍ</span><h1>Un mundo por explorar.<br> <em>Todo conectado.</em></h1><p>Personas, experiencias y destinos.<br>Una nueva forma de hacer turismo.</p></div>
            <div class="globe-scene" aria-hidden="true">
                <div class="orbit orbit-one"></div><div class="orbit orbit-two"></div>
                <svg class="globe" viewBox="0 0 600 600" fill="none">
                    <defs><radialGradient id="globe-glow"><stop stop-color="#1b9b99" stop-opacity=".35"/><stop offset="1" stop-color="#052e36" stop-opacity=".1"/></radialGradient><clipPath id="sphere"><circle cx="300" cy="300" r="216"/></clipPath></defs>
                    <circle cx="300" cy="300" r="216" fill="url(#globe-glow)" stroke="#65e5d4" stroke-opacity=".45"/>
                    <g clip-path="url(#sphere)" stroke="#49b5ae" stroke-opacity=".24">
                        <ellipse cx="300" cy="300" rx="75" ry="216"/><ellipse cx="300" cy="300" rx="155" ry="216"/>
                        <ellipse cx="300" cy="300" rx="216" ry="75"/><ellipse cx="300" cy="300" rx="216" ry="155"/>
                        <path d="M84 300h432M300 84v432M109 200h382M109 400h382"/>
                    </g>
                    <g class="globe-land" fill="#62d8c9" fill-opacity=".23" stroke="#85e9d7" stroke-opacity=".6" stroke-width="1.5">
                        <path d="M145 175l34-24 43 4 28 21 37 7-6 31-32 9-10 30-24 9-22-20-27-7-12-27-22-9zM233 264l32 6 26 24 35 9 7 32-23 24-12 39-27 47-18-10-5-49-17-32-18-39zM343 179l28-22 47 7 19 20 49 11 15 27-20 26-37-8-24 19-21-12-12-31-24-7zM342 249l43 4 27 30-6 48-27 45-23-14-8-43-22-30zM430 384l40-15 29 26-13 24-43-1z"/>
                    </g>
                    <g stroke="#9affdf" stroke-width="1.5" stroke-dasharray="5 7" class="route-path"><path d="M260 332Q290 115 406 219"/><path d="M260 332Q397 270 456 393"/><path d="M193 206Q276 129 406 219"/></g>
                    <g fill="#b7ffe7"><circle cx="260" cy="332" r="5"/><circle cx="406" cy="219" r="5"/><circle cx="193" cy="206" r="4"/><circle cx="456" cy="393" r="4"/></g>
                    <circle class="globe-pulse" cx="260" cy="332" r="16" stroke="#b7ffe7"/>
                </svg>
                <div class="float-tag tag-peru"><span class="tag-dot"></span><div>Perú<span>Un destino. Mil posibilidades.</span></div><b>↗</b></div>
                <div class="float-tag tag-connect"><span class="tag-orbit">✧</span><div>Conexiones que inspiran<span>Tu próxima experiencia te espera</span></div></div>
                <span class="coordinate coordinate-one">13°09′47″ S · 72°32′44″ W</span><span class="coordinate coordinate-two">EXPLORA / CONECTA / DESCUBRE</span>
            </div>
            <div class="art-footer"><span>DISEÑADO PARA IR MÁS LEJOS</span><span>01 — ∞</span></div>
        </section>
        <section class="login-content">
            <div class="login-top"><span class="small-brand">TourismCloud AI</span><a href="{{ route('catalogo.atractivos.index') }}">Explorar destinos <span>↗</span></a></div>
            <div class="login-form-wrap">
                <div class="access-icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="5" y="10" width="14" height="11" rx="3"/><path d="M8 10V7a4 4 0 018 0v3M12 14v3"/></svg></div>
                <span class="form-kicker">TU ESPACIO, SIN FRONTERAS</span><h2>Qué bueno verte<br>de nuevo<span>.</span></h2><p class="form-description">Inicia sesión y continúa tu próximo gran viaje.</p>
                <form id="login-form">
                    <label for="email">Correo electrónico o teléfono</label><div class="field-icon"><span aria-hidden="true">＠</span><input id="email" name="identifier" type="text" autocomplete="username" placeholder="Tu correo o teléfono con código de país" required></div>
                    <div class="label-row"><label for="password">Contraseña</label><button type="button" class="text-button" id="forgot-password">¿La olvidaste?</button></div>
                    <div class="field-icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true"><rect x="5" y="10" width="14" height="11" rx="3"/><path d="M8 10V7a4 4 0 018 0v3"/></svg><input id="password" name="password" type="password" autocomplete="current-password" minlength="6" placeholder="Ingresa tu contraseña" required><button type="button" id="toggle-password" aria-label="Mostrar contraseña" aria-pressed="false">◉</button></div>
                    <label class="checkbox-label"><input type="checkbox" id="remember"> Recordar mi contacto</label>
                    <p id="login-error" class="form-error" role="alert" hidden></p>
                    <button class="primary-button login-submit" type="submit"><span>Iniciar sesión</span><span aria-hidden="true">→</span></button>
                </form>
                <p class="signup-prompt">¿Aún no tienes una cuenta? <button type="button" id="open-registration">Crear cuenta <span aria-hidden="true">↗</span></button></p>
                <div class="divider"><span>o descubre la experiencia</span></div>
                <button class="demo-button" id="demo-access"><span aria-hidden="true">◇</span> Explorar demo interactiva <span aria-hidden="true">↗</span></button>
                <p class="demo-caption">Vista previa del panel · Sin credenciales reales</p>
                <div class="access-note"><span>✧</span><p>Un solo acceso para todo tu ecosistema.<br><strong>Tu organización. Tu equipo. Tus destinos.</strong></p></div>
            </div>
            <footer class="login-footer"><span>© {{ date('Y') }} TourismCloud AI</span><span>Hecho para conectar.</span></footer>
        </section>
    </main>
    <dialog id="recovery-dialog" class="editor-dialog"><form method="dialog"><button class="dialog-close" aria-label="Cerrar">×</button></form><span class="form-kicker">RECUPERA TU ACCESO</span><h2>Volvamos a conectar.</h2><p>La recuperación de contraseña estará disponible al conectar la autenticación. Por ahora puedes explorar el panel con la demo interactiva.</p><button class="primary-button" id="recovery-demo">Explorar demo →</button></dialog>
    <dialog id="registration-dialog" class="editor-dialog registration-dialog" aria-labelledby="registration-title">
        <button type="button" class="dialog-close" id="close-registration" aria-label="Cerrar registro">×</button>
        <div id="registration-progress" class="registration-progress" aria-label="Paso 1 de 2"><span class="current">1 <span>Sobre ti</span></span><i></i><span id="security-step">2 <span>Tu acceso</span></span></div>
        <span class="form-kicker">UN MUNDO DE POSIBILIDADES</span><h2 id="registration-title">Tu próxima aventura<br>empieza aquí<span>.</span></h2><p id="registration-description">Crea tu perfil con correo o teléfono. Tú eliges cómo conectar.</p>
        <form id="registration-form">
            <fieldset id="registration-personal" class="registration-step">
                <legend class="sr-only">Tus datos de contacto</legend>
                <label for="register-name">¿Cómo te llamas?</label><input id="register-name" name="nombre" autocomplete="name" placeholder="Tu nombre y apellido" maxlength="100" required aria-describedby="name-error"><p class="field-error" id="name-error" hidden></p>
                <fieldset class="contact-choice"><legend>¿Cómo prefieres registrarte?</legend><div><label><input type="radio" name="contact-method" value="email" checked><span>＠ Correo electrónico</span></label><label><input type="radio" name="contact-method" value="phone"><span>♧ Teléfono</span></label></div></fieldset>
                <div id="register-email-group"><label for="register-email">Correo electrónico</label><input id="register-email" name="email" type="email" autocomplete="email" placeholder="nombre@ejemplo.com" maxlength="150" required aria-describedby="contact-hint contact-error"></div>
                <div id="register-phone-group" hidden><label for="register-phone">Número de teléfono</label><input id="register-phone" name="telefono" type="tel" autocomplete="tel" placeholder="+51 999 123 456" maxlength="25" disabled aria-describedby="contact-hint contact-error"></div>
                <p id="contact-hint" class="field-hint">Usaremos este correo para verificar tu cuenta cuando el servicio esté disponible.</p><p id="contact-error" class="field-error" hidden></p>
                <button type="button" id="registration-next" class="primary-button registration-submit">Continuar <span aria-hidden="true">→</span></button>
            </fieldset>
            <fieldset id="registration-security" class="registration-step" hidden disabled>
                <legend class="sr-only">Configura tu acceso</legend>
                <div class="contact-summary"><span aria-hidden="true">✓</span><div><strong id="registration-summary-name"></strong><small id="registration-summary-contact"></small></div><button type="button" id="registration-back">Cambiar</button></div>
                <label for="register-password">Crea una contraseña</label><div class="registration-password"><input id="register-password" name="password" type="password" autocomplete="new-password" minlength="10" maxlength="128" placeholder="Una frase que solo tú conozcas" required aria-describedby="password-hint password-strength password-error"><button type="button" id="toggle-register-password" aria-label="Mostrar contraseña de registro" aria-pressed="false">◉</button></div>
                <p id="password-hint" class="field-hint">Al menos 10 caracteres. Puedes usar una frase larga; no hace falta repetirla.</p><div class="password-meter" aria-hidden="true"><span id="password-meter-fill"></span></div><p id="password-strength" class="field-hint" aria-live="polite">Escribe una contraseña para ver su fortaleza.</p><p id="password-error" class="field-error" hidden></p>
                <div class="registration-note"><span aria-hidden="true">◇</span><p>Estás probando el registro. Tus datos y contraseña no se enviarán ni se guardarán.</p></div>
                <button type="submit" class="primary-button registration-submit">Probar creación de cuenta <span aria-hidden="true">→</span></button>
            </fieldset>
        </form>
        <div id="registration-success" class="registration-success" hidden tabindex="-1"><span class="success-orbit" aria-hidden="true">✓</span><h3>¡Todo listo para conectar!</h3><p>Completaste el registro de demostración. La creación de una cuenta real y la verificación de tu contacto estarán disponibles al conectar el backend.</p><button type="button" id="registration-explore" class="primary-button registration-submit">Explorar la demo <span aria-hidden="true">→</span></button><button type="button" id="registration-login" class="registration-return">Volver al inicio de sesión</button></div>
        <p class="registration-existing" id="registration-existing">¿Ya tienes una cuenta? <button type="button" id="registration-signin">Iniciar sesión</button></p>
    </dialog>
    <div id="toast" role="status" class="toast" hidden></div>
</body>
</html>
