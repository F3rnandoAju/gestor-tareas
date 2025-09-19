<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Gestor de Tareas - Software Factory</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap principal -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Mis estilos CSS -->
  <link href="estilos.css" rel="stylesheet">

  <!-- Bootstrap Icons (para íconos del form, footer y botones) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

  <!-- ================= HEADER ================= -->
  <!-- Barra superior con logo, título y botones -->
  <header class="shadow-sm bg-white sticky-top">
    <div class="container d-flex justify-content-between align-items-center py-3">

      <!-- Logo cuadradito azul + texto -->
      <div class="d-flex align-items-center">
        <div class="logo-icon bg-primary text-white fw-bold d-flex justify-content-center align-items-center me-2">
          SF
        </div>
        <h2 class="mb-0 fw-bold text-dark">Software Factory</h2>
      </div>

      <!-- Botón para abrir panel de colores + crear nueva tarea -->
      <div class="d-flex align-items-center">
        <button id="toggleThemePanel" class="btn btn-outline-secondary btn-sm me-2">
          🎨 Personalizar
        </button>
        <a href="#" class="btn btn-primary btn-sm">
          🚀 Nueva Tarea
        </a>
      </div>
    </div>
  </header>

  <!-- CSS interno SOLO para el logo -->
  <style>
    .logo-icon {
      width: 40px;
      height: 40px;
      border-radius: 10px; /* bordes redondeados */
      font-size: 18px;
    }
  </style>


  <!-- ================= PANEL DE PERSONALIZACIÓN ================= -->
  <!-- Panel oculto (se abre con el botón "Personalizar") -->
  <!-- Aquí se cambian los colores del tema y se guardan en localStorage -->
  <div class="container py-4" id="themePanel" style="display:none;">
    <div class="card mb-4 shadow-sm">
      <div class="card-body">
        <h5 class="mb-3">Personalizar tema</h5>

        <div class="row g-2">
          <!-- Cada input es un color editable -->
          <div class="col-md-3"><label>Fondo</label><input type="color" id="colorFondo" value="#ffffff" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Texto</label><input type="color" id="colorTexto" value="#000000" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Header</label><input type="color" id="colorHeader" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Footer</label><input type="color" id="colorFooter" value="#212529" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Botones</label><input type="color" id="colorBoton" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Color principal</label><input type="color" id="colorPrincipal" value="#0d6efd" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Pendiente</label><input type="color" id="colorPendiente" value="#ff7f07" class="form-control form-control-color"></div>
          <div class="col-md-3"><label>Completada</label><input type="color" id="colorCompletada" value="#198754" class="form-control form-control-color"></div>
                  <div class="col-md-3"><label>Revision</label><input type="color" id="revision" value="#226fe2" class="form-control form-control-color"></div>

        </div>

        <!-- Botón para resetear todo a colores por defecto -->
        <div class="mt-3">
          <button id="resetColors" class="btn btn-secondary btn-sm">Restablecer Colores</button>
        </div>
      </div>
    </div>
  </div>


  <!-- ================= FORMULARIO DE TAREAS ================= -->
  <!-- Form para crear/editar tareas -->
  <div class="container py-4">
    <div class="card shadow-lg border-0 rounded-4">
      <div class="card-header bg-primary text-white rounded-top-4">
        <h5 class="mb-0"><i class="bi bi-journal-plus me-2"></i> Nueva Tarea</h5>
      </div>
      <div class="card-body p-4">
        <form id="taskForm">
          <!-- hidden para guardar el id cuando se edita -->
          <input type="hidden" id="taskId" name="id" value="">
          
          <!-- Campo Título -->
          <div class="mb-3">
            <label for="titulo" class="form-label fw-semibold">Título</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-pencil"></i></span>
              <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ej: Implementar login" required>
            </div>
          </div>
          
          <!-- Campo Descripción -->
          <div class="mb-3">
            <label for="descripcion" class="form-label fw-semibold">Descripción</label>
            <div class="input-group">
              <span class="input-group-text bg-light"><i class="bi bi-card-text"></i></span>
              <textarea class="form-control" id="descripcion" name="descripcion" rows="3" placeholder="Detalles de la tarea..."></textarea>
            </div>
          </div>
          
          <!-- Campo Fecha límite + Botón guardar -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="fecha_limite" class="form-label fw-semibold">Fecha límite</label>
              <div class="input-group">
                <span class="input-group-text bg-light"><i class="bi bi-calendar-event"></i></span>
                <input type="date" id="fecha_limite" name="fecha_limite" class="form-control">
              </div>
            </div>
            
            <!-- Botón de guardar -->
            <div class="col-md-6 d-flex align-items-end mb-3">
              <button class="btn btn-success w-100 d-flex align-items-center justify-content-center" id="saveBtn">
                <i class="bi bi-check-circle me-2"></i> Guardar tarea
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- ================= BUSCADOR ================= -->
  <!-- Input para buscar tareas por título -->
  <div class="container mt-3">
    <input type="text" id="searchTask" class="form-control" placeholder="🔍 Buscar tarea por título...">
  </div>


  <!-- ================= LISTADO DE TAREAS ================= -->
  <!-- Aquí se renderizan las tareas dinámicamente con scripts.js -->
  <div id="tasks" class="row g-3">
    
    <!-- Columna Pendientes -->
    <div class="col-md-4">
      <h5 class="text-center">Pendientes</h5>
      <div id="pendientes" class="d-flex flex-column gap-3"></div>
    </div>

    <!-- Columna En revisión -->
    <div class="col-md-4">
      <h5 class="text-center">En Revisión</h5>
      <div id="revision" class="d-flex flex-column gap-3"></div>
    </div>

    <!-- Columna Completadas -->
    <div class="col-md-4">
      <h5 class="text-center">Completadas</h5>
      <div id="completadas" class="d-flex flex-column gap-3"></div>
    </div>
  </div>


  <!-- ================= FOOTER ================= -->
  <!-- Info de la empresa + enlaces rápidos + redes sociales -->
  <footer class="mt-5 bg-dark text-light pt-5 pb-4">
    <div class="container">
      <div class="row text-center text-md-start">
        
        <!-- Columna info empresa -->
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Software Factory</h5>
          <p class="small">
            Gestor de Tareas Personalizable.<br>
            Soluciones web modernas y eficientes para tu equipo.
          </p>
        </div>

        <!-- Columna enlaces rápidos -->
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Enlaces Rápidos</h5>
          <ul class="list-unstyled">
            <li><a href="#" class="text-light text-decoration-none d-block py-1">Inicio</a></li>
            <li><a href="#" class="text-light text-decoration-none d-block py-1">Acerca de</a></li>
            <li><a href="#" class="text-light text-decoration-none d-block py-1">Soporte</a></li>
            <li><a href="#" class="text-light text-decoration-none d-block py-1">Contacto</a></li>
          </ul>
        </div>

        <!-- Columna redes sociales -->
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Síguenos</h5>
          <div class="d-flex justify-content-center justify-content-md-start gap-3">
            <a href="https://www.facebook.com/SigelServ/" class="text-light fs-5"><i class="bi bi-facebook"></i></a>
            <a href="https://gt.linkedin.com/company/sigel_servicios" class="text-light fs-5"><i class="bi bi-linkedin"></i></a>
          </div>
        </div>
      </div>

      <hr class="border-light">

      <div class="text-center small">
        <p class="mb-0">&copy; 2025 <strong>Software Factory</strong>. Todos los derechos reservados.</p>
      </div>
    </div>
  </footer>


  <!-- ================= SCRIPTS ================= -->
  <!-- Librerías externas + mi scripts.js (toda la lógica) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="scripts.js"></script>
</body>
</html>
