<?php
// ================== CABECERA ==================
// Todo lo que salga de acá es JSON (importante para AJAX)
header('Content-Type: application/json; charset=utf-8');

// ================== INCLUDES ==================
// Conexión a la BD y el modelo de tareas
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../modelos/TareaModelo.php';
$model = new TareaModelo($pdo);

// ================== REQUEST ==================
// Método HTTP (GET, POST, PUT, DELETE)
$method = $_SERVER['REQUEST_METHOD'];
// Acción que viene por query string: ?action=list, ?action=create, etc.
$path = $_GET['action'] ?? '';

try {
    // -------- LISTAR TAREAS --------
    // GET + action=list → devuelve todas las tareas
    if($method==='GET' && $path==='list'){
        echo json_encode(['success'=>true,'data'=>$model->listar()]);
        exit;
    }

    // -------- OBTENER UNA TAREA --------
    // GET + action=get&id=X → devuelve esa tarea
    if($method==='GET' && $path==='get' && isset($_GET['id'])){
        echo json_encode(['success'=>true,'data'=>$model->obtener((int)$_GET['id'])]);
        exit;
    }

    // ================== INPUT GENERAL ==================
    // Leo datos de JSON o de un POST normal
    $input=json_decode(file_get_contents('php://input'),true)??$_POST;

    // -------- CREAR --------
    // POST + action=create → inserta una nueva tarea
    if($method==='POST' && $path==='create'){
        $titulo=trim($input['titulo']??'');

        // Validación básica: el título es obligatorio
        if($titulo===''){
            http_response_code(422); 
            echo json_encode(['success'=>false,'error'=>'El título es obligatorio']); 
            exit;
        }

        $descripcion=$input['descripcion']??'';
        $estado=in_array($input['estado']??'pendiente',['pendiente','en revision','completada'])
                 ? $input['estado'] : 'pendiente';
        $fecha_limite=$input['fecha_limite']??null;

        $id=$model->crear($titulo,$descripcion,$estado,$fecha_limite);

        echo json_encode(['success'=>true,'id'=>$id]); 
        exit;
    }

// -------- ACTUALIZAR --------
// POST/PUT + action=update → modifica tarea existente
if(($method==='POST' || $method==='PUT') && $path==='update'){
    $id = (int)($input['id'] ?? 0);
    if(!$id){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'ID inválido']);
        exit;
    }

    // Traigo tarea actual
    $tarea = $model->obtener($id);
    if(!$tarea){
        http_response_code(404);
        echo json_encode(['success'=>false,'error'=>'Tarea no encontrada']);
        exit;
    }

    // Solo actualizar estado si viene en el payload, sino conservar actual
    $estado = in_array($input['estado'] ?? '', ['pendiente','en revision','completada']) 
              ? $input['estado'] 
              : $tarea['estado'];
              
    $titulo = trim($input['titulo'] ?? $tarea['titulo']);
    $descripcion = trim($input['descripcion'] ?? $tarea['descripcion']);
    $fecha_limite = trim($input['fecha_limite'] ?? $tarea['fecha_limite']);

    // Validación rápida: título, descripción y fecha no pueden estar vacíos
    if($titulo==='' || $descripcion==='' || $fecha_limite===''){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'Todos los campos son obligatorios']);
        exit;
    }

    $success = $model->actualizar($id, $titulo, $descripcion, $estado, $fecha_limite);
    echo json_encode(['success'=>$success]);
    exit;
}

// NO TOCAR ESTO FUNCIONA
    // -------- ELIMINAR --------
    // DELETE o POST + action=delete → borra por id
    if(($method==='DELETE'||$method==='POST') && $path==='delete'){
        $id=(int)($input['id']??$_GET['id']??0);
        if(!$id){
            http_response_code(422); 
            echo json_encode(['success'=>false,'error'=>'ID inválido']); 
            exit;
        }
        echo json_encode(['success'=>$model->eliminar($id)]);
        exit;
    }

    // Si no matchea nada → error 400
    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Ruta no encontrada.']);

}catch(Exception $e){
    // Error inesperado del servidor
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Error interno: '.$e->getMessage()]);
}
