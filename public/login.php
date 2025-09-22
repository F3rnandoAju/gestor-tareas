<?php
session_start();
require_once "../app/conexion.php"; // conexión PDO

// --- REGISTRO ---
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user"; // por defecto usuario normal

    $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, role) VALUES (:u, :p, :r)");
    try {
        $stmt->execute([":u" => $username, ":p" => $password, ":r" => $role]);
        $_SESSION['msg'] = "✅ Usuario registrado con éxito, ahora inicia sesión.";
    } catch (Exception $e) {
        $_SESSION['msg'] = "❌ Error: el usuario ya existe o problema con la BD.";
    }
}

// --- LOGIN ---
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = :u");
    $stmt->execute([":u" => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];   // 👈 aquí guardas el id real de la BD
    $_SESSION['user']    = $user['username'];
    $_SESSION['role']    = $user['role'];

    header("Location: index.php");
    exit;
}
else {
        $_SESSION['msg'] = "⚠️ Usuario o contraseña incorrectos.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Gestor de Tareas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background: linear-gradient(135deg, #0d6efd, #6610f2);
      font-family: 'Segoe UI', sans-serif;
    }
    .container-box {
      background: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0,0,0,0.3);
      width: 100%;
      max-width: 400px;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    h2 {
      margin-bottom: 1rem;
      font-weight: bold;
      color: #0d6efd;
    }
    .toggle-link {
      cursor: pointer;
      color: #6610f2;
      font-size: 0.9rem;
    }
    .form-box {
      transition: transform 0.6s ease-in-out;
      width: 200%;
      display: flex;
    }
    .form-section {
      width: 50%;
      padding: 1rem;
    }
    .form-container {
      overflow: hidden;
      position: relative;
      width: 100%;
    }
    .msg {
      margin-bottom: 1rem;
      font-size: 0.9rem;
      color: red;
    }
  </style>
</head>
<body>
  <div class="container-box">
    <?php if(isset($_SESSION['msg'])): ?>
      <div class="msg"><?= $_SESSION['msg']; unset($_SESSION['msg']); ?></div>
    <?php endif; ?>

    <div class="form-container">
      <div class="form-box" id="formBox">
        <!-- LOGIN -->
        <div class="form-section">
          <h2>Iniciar Sesión</h2>
          <form method="post">
            <input type="text" name="username" class="form-control mb-2" placeholder="Usuario" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Contraseña" required>
            <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
          </form>
          <p class="mt-3">¿No tienes cuenta? <span class="toggle-link" onclick="toggleForm()">Regístrate</span></p>
        </div>

        <!-- REGISTRO -->
        <div class="form-section">
          <h2>Registro</h2>
          <form method="post">
            <input type="text" name="username" class="form-control mb-2" placeholder="Nuevo usuario" required>
            <input type="password" name="password" class="form-control mb-2" placeholder="Nueva contraseña" required>
            <button type="submit" name="register" class="btn btn-success w-100">Crear Cuenta</button>
          </form>
          <p class="mt-3">¿Ya tienes cuenta? <span class="toggle-link" onclick="toggleForm()">Inicia sesión</span></p>
        </div>
      </div>
    </div>
  </div>

  <script>
    let showingLogin = true;
    function toggleForm(){
      const box = document.getElementById('formBox');
      if(showingLogin){
        box.style.transform = "translateX(-50%)";
      } else {
        box.style.transform = "translateX(0%)";
      }
      showingLogin = !showingLogin;
    }
  </script>
</body>
</html>
