<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sorteo Magno - Acceso Biométrico</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Rajdhani:wght@300;500;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        :root { --neon: #00f2ff; --purple: #7000ff; --glass: rgba(15, 23, 42, 0.8); }
        body { 
            margin: 0; background: #020617; color: white; 
            font-family: 'Rajdhani', sans-serif; height: 100vh;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
        }

        /* Fondo Animado */
        .bg {
            position: fixed; width: 100vw; height: 100vh; z-index: -1;
            background: radial-gradient(circle at center, #1e1b4b 0%, #020617 100%);
        }
        .stars {
            position: absolute; width: 200%; height: 200%;
            background: url('https://www.transparenttextures.com/patterns/stardust.png');
            animation: move 100s linear infinite; opacity: 0.4;
        }
        @keyframes move { from { transform: translate(0,0); } to { transform: translate(-50%,-50%); } }

        /* Card Futurista */
        .auth-card {
            background: var(--glass); padding: 40px; border-radius: 25px;
            border: 1px solid rgba(0, 242, 255, 0.2); width: 400px;
            backdrop-filter: blur(15px); box-shadow: 0 0 40px rgba(0,0,0,0.8);
            position: relative;
        }
        .auth-card::before {
            content: ''; position: absolute; inset: -1px;
            background: linear-gradient(45deg, var(--neon), transparent, var(--purple));
            z-index: -1; border-radius: 26px; opacity: 0.3;
        }

        h1 { font-family: 'Orbitron', sans-serif; text-align: center; font-size: 1.5rem; color: var(--neon); text-shadow: 0 0 10px var(--neon); margin-bottom: 30px; }
        
        .field { margin-bottom: 15px; }
        label { display: block; font-size: 0.7rem; color: var(--neon); text-transform: uppercase; margin-bottom: 5px; }
        input {
            width: 100%; padding: 12px; background: rgba(0,0,0,0.5); border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px; color: white; transition: 0.3s; box-sizing: border-box;
        }
        input:focus { outline: none; border-color: var(--neon); box-shadow: 0 0 10px rgba(0,242,255,0.3); }

        .btn {
            width: 100%; padding: 14px; margin-top: 15px; border: none; border-radius: 8px;
            background: linear-gradient(45deg, var(--neon), var(--purple));
            color: white; font-family: 'Orbitron', sans-serif; font-weight: bold;
            cursor: pointer; transition: 0.3s;
        }
        .btn:hover { transform: scale(1.02); box-shadow: 0 0 20px var(--neon); }

        .toggle { text-align: center; margin-top: 20px; font-size: 0.8rem; color: #94a3b8; }
        .toggle b { color: var(--neon); cursor: pointer; }
        .hidden { display: none; }
    </style>
</head>
<body>

<div class="bg"><div class="stars"></div></div>

<div class="auth-card">
    <div id="login-box">
        <h1>ACCESS_GRANTED</h1>
        <div class="field">
            <label>Identidad (User/Email)</label>
            <input type="text" id="l_user">
        </div>
        <div class="field">
            <label>Código de Encriptación</label>
            <input type="password" id="l_code">
        </div>
        <button class="btn" onclick="handleLogin()">INICIAR SISTEMA</button>
        <div class="toggle">¿Nuevo usuario? <b onclick="toggle()">CREAR REGISTRO</b></div>
    </div>

    <div id="reg-box" class="hidden">
        <h1>NEW_IDENTITY</h1>
        <div class="field"><label>Nombre Completo</label><input type="text" id="r_nombre"></div>
        <div class="field"><label>Email</label><input type="email" id="r_correo"></div>
        <div class="field"><label>Username</label><input type="text" id="r_user"></div>
        <div class="field"><label>Código Especial</label><input type="password" id="r_code"></div>
        <button class="btn" onclick="handleRegister()">REGISTRAR EN BD</button>
        <div class="toggle">¿Ya tienes cuenta? <b onclick="toggle()">LOG IN</b></div>
    </div>
</div>

<script>
    function toggle() {
        $("#login-box, #reg-box").toggleClass("hidden");
    }

    function handleRegister() {
        const data = {
            action: 'register',
            nombre: $("#r_nombre").val(),
            correo: $("#r_correo").val(),
            user: $("#r_user").val(),
            code: $("#r_code").val()
        };

        // Añadimos 'json' como cuarto parámetro para procesar la respuesta correctamente
        $.post('auth.php', data, function(res) {
            if(res.status === "success") {
                alert("🚀 Registro exitoso. Ahora puedes loguearte.");
                toggle();
            } else {
                alert("❌ Error: " + (res.msg || "No se pudo completar el registro"));
            }
        }, 'json');
    }

    function handleLogin() {
        const data = {
            action: 'login',
            user: $("#l_user").val(),
            code: $("#l_code").val()
        };

        // Procesamos la respuesta JSON del servidor
        $.post('auth.php', data, function(res) {
            if(res.status === "success") {
                // CLAVE: Guardamos res.username (devuelto por el PHP)
                // Esto garantiza que se guarde el 'nick' y no el correo
                localStorage.setItem('usuarioLogueado', res.username); 
                window.location.href = "admin.html";
            } else {
                // Mostramos el mensaje de error que viene desde auth.php
                alert("⚠️ Acceso denegado: " + (res.msg || "Credenciales no válidas."));
            }
        }, 'json'); 
    }
</script>

</body>
</html>