<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestor de Tareas - Software Factory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="estilos.css" rel="stylesheet">
</head>
<body>
  <!-- HEADER -->
  <header class="mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
      <h1 class="mb-0">Gestor de Tareas - Software Factory</h1>
      <button id="toggleThemePanel" class="btn btn-light btn-sm">🎨 Personalizar Tema</button>
    </div>
  </header>

  <!-- PANEL DE PERSONALIZACIÓN (oculto por defecto) -->
  <div class="container py-4" id="themePanel" style="display:none;">
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Personalizar tema</h5>
        <div class="row g-2">
          <div class="col-md-3"><label>Fondo</label><input type="color" id="colorFondo" value="#ffffff" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Texto</label><input type="color" id="colorTexto" value="#000000" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Header</label><input type="color" id="colorHeader" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Footer</label><input type="color" id="colorFooter" value="#212529" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Botones</label><input type="color" id="colorBoton" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Color principal</label><input type="color" id="colorPrincipal" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Pendiente</label><input type="color" id="colorPendiente" value="#ff7f07" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>En progreso</label><input type="color" id="colorEnProgreso" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Completada</label><input type="color" id="colorCompletada" value="#198754" class="form-control form-control-color"></div>
        </div>
        <div class="mt-3">
          <button id="resetColors" class="btn btn-secondary btn-sm">Restablecer Colores</button>
        </div>
      </div>
    </div>
  </div>

  <!-- FORMULARIO DE TAREAS -->
  <div class="container py-4">
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <form id="taskForm">
          <input type="hidden" id="taskId" name="id" value="">
          <div class="mb-3">
            <label for="titulo" class="form-label">Título</label>
            <input type="text" class="form-control" id="titulo" name="titulo" required>
          </div>
          <div class="mb-3">
            <label for="descripcion" class="form-label">Descripción</label>
            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
          </div>
          <div class="row">
            
            <div class="col-md-4 mb-3">
              <label for="fecha_limite" class="form-label">Fecha límite</label>
              <input type="date" id="fecha_limite" name="fecha_limite" class="form-control">
            </div>
            <div class="col-md-4 d-flex align-items-end mb-3">
              <button class="btn btn-primary w-100" id="saveBtn">Guardar tarea</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- CONTENEDOR DE TAREAS -->
    <div id="tasks" class="row g-3"></div>
  </div>

<!-- FOOTER COMPLETO -->
<footer class="mt-4 bg-dark text-light pt-5 pb-3">
  <div class="container">
    <div class="row">
      <!-- Información de la empresa -->
      <div class="col-md-4 mb-3">
        <h5>Software Factory</h5>
        <p>Gestor de Tareas Personalizable.<br>Soluciones web modernas y eficientes para tu equipo.</p>
      </div>

      <!-- Enlaces rápidos -->
      <div class="col-md-4 mb-3">
        <h5>Enlaces Rápidos</h5>
        <ul class="list-unstyled">
          <li><a href="#" class="text-light text-decoration-none">Inicio</a></li>
          <li><a href="#" class="text-light text-decoration-none">Acerca de</a></li>
          <li><a href="#" class="text-light text-decoration-none">Soporte</a></li>
          <li><a href="#" class="text-light text-decoration-none">Contacto</a></li>
        </ul>
      </div>

      <!-- Redes sociales -->
      <div class="col-md-4 mb-3">
        <h5>Síguenos</h5>
        <a href="#" class="text-light me-2"><i class="bi bi-facebook"></i> Facebook</a><br>
        <a href="#" class="text-light me-2"><i class="bi bi-twitter"></i> Twitter</a><br>
        <a href="#" class="text-light me-2"><i class="bi bi-linkedin"></i> LinkedIn</a><br>
        <a href="#" class="text-light"><i class="bi bi-github"></i> GitHub</a>
      </div>
    </div>

    <hr class="border-light">
    <div class="text-center">
      <p class="mb-0">&copy; 2025 Software Factory. Todos los derechos reservados.</p>
    </div>
  </div>
</footer>

<!-- Para los íconos de redes sociales (Bootstrap Icons) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts.js"></script>
</body>
</html>
