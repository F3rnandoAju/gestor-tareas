$(document).ready(function(){
  const apiUrl = '../app/controladores/tareaControlador.php';

  // ----------------- ALERTAS -----------------
  function showAlert(msg, type='success') {
    const el = $(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
    $('.container').first().prepend(el);
    setTimeout(()=> el.alert('close'), 4000);
  }

  // ----------------- FETCH Y RENDER TAREAS -----------------
  function fetchTasks(){
    $('#tasks').html('<div class="text-center p-4">Cargando...</div>');
    $.get(apiUrl, { action: 'list' })
      .done(res => {
        if(!res.success) { $('#tasks').html('<div class="text-danger">Error cargando tareas</div>'); return; }
        renderTasks(res.data);
      })
      .fail(()=> $('#tasks').html('<div class="text-danger">Error de red</div>'));
  }

  function renderTasks(data){
    if(!data.length){ $('#tasks').html('<div class="col-12"><div class="alert alert-info">No hay tareas. Crea la primera.</div></div>'); return; }
    const html = data.map(t => {
      const estadoClass = t.estado==='pendiente'?'tarea-pendiente':(t.estado==='en progreso'?'tarea-en-progreso':'tarea-completada');
      const fecha = t.fecha_limite?`<small class="text-muted">Límite: ${t.fecha_limite}</small>`:'';
      return `<div class="col-md-6 col-lg-4 fade-in">
        <div class="card tarea-card ${estadoClass} p-2">
          <div class="card-body">
            <h5 class="card-title">${escapeHtml(t.titulo)}</h5>
            <p class="card-text">${escapeHtml(t.descripcion||'')}</p>
            <p>${fecha}</p>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-primary btn-edit" data-id="${t.id}">Editar</button>
              <button class="btn btn-sm btn-outline-danger btn-delete" data-id="${t.id}">Eliminar</button>
              <div class="ms-auto"><span class="badge bg-secondary">${t.estado}</span></div>
            </div>
          </div>
        </div>
      </div>`;
    }).join('');
    $('#tasks').html(html);
  }

  function escapeHtml(text){ return $('<div>').text(text).html(); }

  // ----------------- FORMULARIO -----------------
  $('#taskForm').on('submit', function(e){
    e.preventDefault();
    const id = $('#taskId').val();
    const payload = {
      titulo: $('#titulo').val().trim(),
      descripcion: $('#descripcion').val().trim(),
      estado: $('#estado').val(),
      fecha_limite: $('#fecha_limite').val()
    };
    if(!payload.titulo){ showAlert('El título es obligatorio','warning'); return; }
    const action = id ? 'update' : 'create';
    if(id) payload.id=id;

    $.ajax({
      url: apiUrl+'?action='+action,
      method:'POST',
      contentType:'application/json',
      data: JSON.stringify(payload)
    }).done(res=>{
      if(res.success){ showAlert(id?'Tarea actualizada':'Tarea creada'); resetForm(); fetchTasks(); }
      else showAlert(res.error || 'Error','danger');
    }).fail(()=> showAlert('Error de red','danger'));
  });

  $('#tasks').on('click','.btn-edit', function(){
    const id=$(this).data('id');
    $.get(apiUrl,{action:'get',id:id}).done(res=>{
      if(!res.success){ showAlert('No se encontró la tarea','danger'); return; }
      const t=res.data;
      $('#taskId').val(t.id); $('#titulo').val(t.titulo); $('#descripcion').val(t.descripcion);
      $('#estado').val(t.estado); $('#fecha_limite').val(t.fecha_limite);
      $('html,body').animate({scrollTop:0},400);
    }).fail(()=>showAlert('Error al obtener tarea','danger'));
  });

  $('#tasks').on('click','.btn-delete', function(){
    if(!confirm('¿Eliminar esta tarea?')) return;
    const id=$(this).data('id');
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

  function resetForm(){ $('#taskId').val(''); $('#taskForm')[0].reset(); }

  // ----------------- PERSONALIZACIÓN -----------------
  const colores = [
    {var:'--color-fondo', id:'#colorFondo'},
    {var:'--color-texto', id:'#colorTexto'},
    {var:'--color-header', id:'#colorHeader'},
    {var:'--color-footer', id:'#colorFooter'},
    {var:'--color-boton', id:'#colorBoton'},
    {var:'--color-principal', id:'#colorPrincipal'},
    {var:'--color-pendiente', id:'#colorPendiente'},
    {var:'--color-en-progreso', id:'#colorEnProgreso'},
    {var:'--color-completada', id:'#colorCompletada'}
  ];

  function cambiarColor(variable,color){ document.documentElement.style.setProperty(variable,color); localStorage.setItem(variable,color); }

  colores.forEach(c=>{
    const saved=localStorage.getItem(c.var);
    if(saved) document.documentElement.style.setProperty(c.var,saved);
    $(c.id).val(saved || $(c.id).val()).on('input',function(){ cambiarColor(c.var,$(this).val()); });
  });

  $('#toggleThemePanel').click(()=> $('#themePanel').slideToggle());
  $('#resetColors').click(()=>{
    colores.forEach(c=>{
      const defaultColor=$(c.id).attr('value');
      cambiarColor(c.var,defaultColor);
      $(c.id).val(defaultColor);
    });
  });

  // ----------------- INICIO -----------------
  fetchTasks();
});
