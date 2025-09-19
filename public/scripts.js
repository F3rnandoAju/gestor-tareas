$(document).ready(function(){
  // Ruta del controlador PHP → cambiar si muevo las carpetas
  const apiUrl = '../app/controladores/tareaControlador.php';

  // Guardar todas las tareas en memoria (lo uso en el buscador)
  let allTasks = []; 

  // ================== ALERTAS ==================
  // Mostrar un mensajito arriba. Se cierra solo en 4 seg.
  // Usar para feedback rápido (crear, editar, error, etc.)
  function showAlert(msg, type='success') {
    try {
      const el = $(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
                      ${msg}
                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`);
      $('.container').first().prepend(el);
      setTimeout(()=> el.alert('close'), 4000); 
    } catch(e) {
      console.error("Error mostrando alerta:", e);
    }
  }

  // ================== CARGAR TAREAS ==================
  // Pide todas las tareas al servidor y las pinta
  function fetchTasks(){
    $('#tasks').html('<div class="text-center p-4">Cargando...</div>');

    $.get(apiUrl, { action: 'list' })
      .done(res => {
        if(!res || typeof res !== "object" || !Array.isArray(res.data)) {
          $('#tasks').html('<div class="text-danger">Respuesta inválida del servidor</div>');
          return;
        }
        if(!res.success) { 
          $('#tasks').html('<div class="text-danger">Error cargando tareas</div>'); 
          return; 
        }
        allTasks = res.data || []; // guardar todo
        renderTasks(allTasks);
      })
      .fail(()=> $('#tasks').html('<div class="text-danger">Error de red</div>'));
  }

  // Para evitar inyecciones raras (XSS)
  function escapeHtml(text){ 
    return $('<div>').text(text || "").html(); 
  }

  // ================== RENDER UNA TAREA ==================
  function renderCard(t){
    try {
      if(!t || typeof t !== "object") return "";

      const estado = t.estado ? t.estado.toLowerCase() : "pendiente";
      const estadoClass = estado==='pendiente' ? 'tarea-pendiente' :
                          estado==='en revision' ? 'tarea-en-revision' :
                          'tarea-completada';

      const fecha = t.fecha_limite ? `<small class="text-muted">Límite: ${escapeHtml(t.fecha_limite)}</small>` : '';

      // Botones que aparecen según el estado
      let botones = '';
      const hoy = new Date();
      const fechaLimite = t.fecha_limite ? new Date(t.fecha_limite) : null;
      const limitePasado = fechaLimite && fechaLimite < hoy;

      if(estado === 'pendiente' && !limitePasado){
        botones = `
          <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${t.id || ''}">Editar</button>
          <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${t.id || ''}">Eliminar</button>
          <button class="btn btn-sm btn-outline-warning btn-revision" data-id="${t.id || ''}">Mandar a Revisión</button>
        `;
      } else if(estado === 'en revision'){
        botones = `
          <button class="btn btn-sm btn-outline-success btn-ok" data-id="${t.id || ''}">Completada</button>
          <button class="btn btn-sm btn-outline-secondary btn-revocar" data-id="${t.id || ''}">Revocar</button>
        `;
      }

      return `
        <div class="col-12 col-md-4 mb-4">
          <div class="card tarea-card ${estadoClass} p-2 fade-in">
            <div class="card-body">
              <h5 class="card-title">${escapeHtml(t.titulo)}</h5>
              <p class="card-text">${escapeHtml(t.descripcion||'')}</p>
              <p>${fecha}</p>
              <div class="d-flex gap-2">
                ${botones}
                <div class="ms-auto"><span class="badge bg-secondary">${escapeHtml(estado)}</span></div>
              </div>
            </div>
          </div>
        </div>
      `;
    } catch(e) {
      console.error("Error renderizando tarjeta:", e);
      return "";
    }
  }

  // ================== RENDER GENERAL ==================
  // Pinta todas las tareas separadas por estado
  function renderTasks(data){
    try {
      if(!Array.isArray(data) || !data.length){ 
        $('#tasks').html('<div class="alert alert-info">No hay tareas. Crea la primera.</div>'); 
        return; 
      }

      const pendientes = data.filter(t => t && t.estado === 'pendiente');
      const revision = data.filter(t => t && t.estado === 'en revision');
      const completadas = data.filter(t => t && t.estado === 'completada');

      let html = '';
      if(pendientes.length){
        html += `<h5 class="mt-4 text-center">Pendientes</h5><div class="row">`;
        html += pendientes.map(t => renderCard(t)).join('');
        html += `</div>`;
      }

      if(revision.length){
        html += `<h5 class="mt-4 text-center">En Revisión</h5><div class="row">`;
        html += revision.map(t => renderCard(t)).join('');
        html += `</div>`;
      }

      if(completadas.length){
        html += `<h5 class="mt-4 text-center text-muted">Completadas</h5><div class="row">`;
        html += completadas.map(t => renderCard(t)).join('');
        html += `</div>`;
      }

      $('#tasks').html(html);
    } catch(e) {
      console.error("Error renderizando tareas:", e);
      $('#tasks').html('<div class="text-danger">Error mostrando tareas</div>');
    }
  }

  // ================== FORMULARIO ==================
  // Crear o editar tarea
 $('#taskForm').on('submit', function(e){
  e.preventDefault();
  try {
    const id = $('#taskId').val();
    const payload = {
      titulo: $('#titulo').val().trim(),
      descripcion: $('#descripcion').val().trim(),
      fecha_limite: $('#fecha_limite').val()
    };

    // Validaciones rápidas
    if(!payload.titulo){ showAlert('El título es obligatorio','warning'); return; }
    if(!payload.descripcion){ showAlert('La descripción es obligatoria','warning'); return; }
    if(!payload.fecha_limite){ showAlert('La fecha límite es obligatoria','warning'); return; }

    // Si hay id → editar, si no → crear
    let action = 'create';
    if(id){ 
      payload.id = id; 
      action = 'update'; 
    } else {
      payload.estado = 'pendiente'; 
    }

    $.ajax({
      url: apiUrl+'?action='+action,
      method:'POST',
      contentType:'application/json',
      data: JSON.stringify(payload)
    }).done(res=>{
      if(res && res.success){ 
        showAlert(id?'Tarea actualizada':'Tarea creada'); 
        resetForm(); 
        fetchTasks(); 
      } else {
        showAlert((res && res.error) || 'Error','danger');
      }
    }).fail(()=> showAlert('Error de red','danger'));
  } catch(e) {
    console.error("Error en submit:", e);
    showAlert('Error inesperado','danger');
  }
});

  // ================== EDITAR DESDE BOTÓN ==================
  $('#tasks').on('click','.btn-edit', function(){
    const id=$(this).data('id');
    if(!id) return;
    $.get(apiUrl,{action:'get',id:id}).done(res=>{
      if(!res.success || !res.data){ showAlert('No se encontró la tarea','danger'); return; }
      const t=res.data;
      // Relleno el formulario con los datos de la tarea
      $('#taskId').val(t.id||''); 
      $('#titulo').val(t.titulo||''); 
      $('#descripcion').val(t.descripcion||'');
      $('#estado').val(t.estado||'pendiente'); 
      $('#fecha_limite').val(t.fecha_limite||'');
      $('html,body').animate({scrollTop:0},400);
    }).fail(()=>showAlert('Error al obtener tarea','danger'));
  });

  // ================== CAMBIOS DE ESTADO ==================
  // Función genérica para cambiar estado de una tarea
  function updateTaskStatus(id, nuevoEstado, mensaje){
    if(!id) return;
    $.get(apiUrl, { action: 'get', id: id })
      .done(res=>{
        if(!res.success || !res.data){ showAlert('No se encontró la tarea','danger'); return; }
        const t = res.data;
        const payload = {
          id: id,
          titulo: t.titulo || '',
          descripcion: t.descripcion || '',
          fecha_limite: t.fecha_limite || null,
          estado: nuevoEstado
        };
        $.ajax({
          url: apiUrl+'?action=update',
          method: 'POST',
          contentType: 'application/json',
          data: JSON.stringify(payload)
        }).done(res2=>{
          if(res2.success){ showAlert(mensaje,'success'); fetchTasks(); }
          else showAlert(res2.error || 'Error al actualizar tarea','danger');
        }).fail(()=> showAlert('Error de red','danger'));
      }).fail(()=> showAlert('Error al obtener tarea','danger'));
  }

  // Botones que llaman a updateTaskStatus
  $('#tasks').on('click','.btn-revision', function(){ updateTaskStatus($(this).data('id'), 'en revision', 'Tarea enviada a revisión'); });
  $('#tasks').on('click','.btn-ok', function(){ updateTaskStatus($(this).data('id'), 'completada', 'Tarea marcada como completada'); });
  $('#tasks').on('click','.btn-revocar', function(){ updateTaskStatus($(this).data('id'), 'pendiente', 'Tarea revocada a pendiente'); });

  // ================== ELIMINAR ==================
  $('#tasks').on('click','.btn-delete', function(){
    const id=$(this).data('id');
    if(!id) return;
    if(!confirm('¿Eliminar esta tarea?')) return;
    $.ajax({
      url: apiUrl+'?action=delete',
      method:'POST',
      contentType:'application/json',
      data: JSON.stringify({id:id})
    }).done(res=>{
      if(res.success){ showAlert('Tarea eliminada','info'); fetchTasks(); }
      else showAlert(res.error||'Error','danger');
    }).fail(()=>showAlert('Error de red','danger'));
  });

  // Resetear formulario (limpiar inputs)
  function resetForm(){ $('#taskId').val(''); $('#taskForm')[0].reset(); }

  // ================== PERSONALIZACIÓN ==================
  // Colores configurables (se guardan en localStorage)
  const colores = [
    {var:'--color-fondo', id:'#colorFondo'},
    {var:'--color-texto', id:'#colorTexto'},
    {var:'--color-header', id:'#colorHeader'},
    {var:'--color-footer', id:'#colorFooter'},
    {var:'--color-boton', id:'#colorBoton'},
    {var:'--color-principal', id:'#colorPrincipal'},
    {var:'--color-pendiente', id:'#colorPendiente'},
    {var:'--color-en-revision', id:'#colorEnRevision'},
    {var:'--color-completada', id:'#colorCompletada'}
  ];

  // Aplico los colores guardados
  colores.forEach(c=>{
    const saved=localStorage.getItem(c.var);
    if(saved) document.documentElement.style.setProperty(c.var,saved);
    $(c.id).val(saved || $(c.id).val()).on('input',function(){ 
      document.documentElement.style.setProperty(c.var,$(this).val()); 
      localStorage.setItem(c.var,$(this).val()); 
    });
  });

  // Mostrar/ocultar panel de colores
  $('#toggleThemePanel').click(()=> $('#themePanel').slideToggle());

  // Resetear colores a los valores por defecto
  $('#resetColors').click(()=>{ 
    colores.forEach(c=>{
      const defaultColor=$(c.id).attr('value');
      document.documentElement.style.setProperty(c.var, defaultColor);
      $(c.id).val(defaultColor);
      localStorage.setItem(c.var, defaultColor);
    });
  });

  // ================== BUSCADOR ==================
  // Filtrar tareas según lo que escriba en el input
  $('#searchTask').on('input', function(){
    try {
      const term = ($(this).val() || "").toLowerCase();
      if(!term) { 
        renderTasks(allTasks); 
        return; 
      }
      const filtered = allTasks.filter(t => {
        if(!t || typeof t.titulo !== "string") return false;
        return t.titulo.toLowerCase().includes(term);
      });
      renderTasks(filtered);
    } catch(e) {
      console.error("Error en buscador:", e);
    }
  });

  // ================== INICIO ==================
  // Apenas carga la página → pido las tareas
  fetchTasks();
}); 
