# Gestor de Tareas - Empresa Web

Aplicación web para gestionar tareas con estados (pendiente, en revisión, completada), personalización de colores y buscador integrado.

---

## Tecnologías utilizadas

* **Backend:** PHP (PDO)
* **Base de datos:** MySQL (phpMyAdmin)
* **Frontend:** HTML, CSS, Bootstrap 5, jQuery

> Elegí estas tecnologías porque son las que aprendí durante mi estadía en **SIGEL** y en mi institución.

---

## Instalación y configuración

1. **Clonar este repositorio** en tu carpeta de proyectos.
2. **Importar la base de datos** con el archivo `empresa_web.sql` en **phpMyAdmin**.
3. **Levantar XAMPP** (Apache + MySQL).
4. Configurar las credenciales en `app/conexion.php` *(o dejar las que vienen por defecto)*.
5. Abrir en el navegador:

   ```
   http://localhost/gestor-tareas/public/
   ```

---

## Endpoints de la API

* `GET  ?action=list` → Listar todas las tareas.
* `GET  ?action=get&id={id}` → Obtener una tarea por su ID.
* `POST ?action=create` → Crear una nueva tarea.
* `POST ?action=update` → Actualizar una tarea existente.
* `POST ?action=delete` → Eliminar una tarea.

---

## Dificultades encontradas

* **APIs / Backend:**
  Una de las mayores dificultades fue desarrollar las APIs. Conocía lo básico, pero no sabía cómo estructurarlas bien. Me apoyé en un proyecto anterior y en tutoriales básicos para lograrlo.

* **Diseño / Frontend:**
  También me costó la parte de los estilos. Además del archivo `estilos.css`, uso algunos estilos desde JS directamente en las casillas correspondientes. Es una técnica que ya manejo bastante bien, pero sigo adaptándome para perfeccionarla al 100%.

---

##  Estado actual

*✅ CRUD de tareas funcional
*✅ Sistema de estados (pendiente, en revisión, completada)
*✅ Personalización de colores desde la interfaz
*✅ Buscador de tareas en tiempo real

