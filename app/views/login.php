<?php require_once __DIR__ . '/../../config/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar Sesión | A&E System</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>css/login.css">
<body>
    
         
    <div class="login-container">
        <div id="loginCard" class="login-card">
            <div class="logo-section">
                <img src="<?= BASE_URL ?>img/logo.jpg" alt="Logo A&E">
                <h2>A&E SYSTEM</h2>
                <p>Acceso al sistema de notificaciones</p>
            </div>
   <!-- ERROR arriba -->
            <div id="errorBox" class="error-box neutral">
      Ingresa tus credenciales
    </div>


            <form id="loginForm" class="login-form" autocomplete="off">
                <div class="form-group">
                    <label>Email</label>
                    <input type="text" id="email" name="email" required placeholder="Ingresa tu correo">
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="Ingresa tu contraseña">
                </div>

                <button type="submit" class="btn-login">Ingresar</button>
            </form>
        </div>
    </div>

<script>
(function(){
    const form = document.getElementById('loginForm');
    const errorBox = document.getElementById('errorBox');
    const loginCard = document.getElementById('loginCard');

    // Animación inicial solo una vez por sesión
    if (!sessionStorage.getItem('loginSeen')) {
        loginCard.classList.add('animated-in');
        sessionStorage.setItem('loginSeen', '1');
    }

    function setNeutralMessage() {
        errorBox.textContent = 'Ingresa tus credenciales';
        errorBox.classList.remove('error');
        errorBox.classList.add('neutral');
    }

    function setErrorMessage(msg) {
        errorBox.textContent = msg;
        errorBox.classList.remove('neutral');
        errorBox.classList.add('error');

        // pequeña animación de resaltado
        form.querySelectorAll('.form-group').forEach(g => g.classList.add('error'));
        setTimeout(() => {
            form.querySelectorAll('.form-group').forEach(g => g.classList.remove('error'));
        }, 2000);
    }

    // Estado inicial
    setNeutralMessage();

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const formData = new FormData(form);

        try {
            const response = await fetch('<?= BASE_URL ?>login/autenticar', {
                method: 'POST',
                body: new URLSearchParams([...formData]),
                
            });

            const data = await response.json();

         if (data.success) {
    window.location.replace(data.redirect);
} else {
    setErrorMessage(data.message || 'Credenciales incorrectas');
}

        } catch (err) {
            setErrorMessage('Error de conexión con el servidor');
        }
    });
})();
</script>


</body>
</html>
