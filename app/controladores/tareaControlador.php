<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__.'/../conexion.php';
require_once __DIR__.'/../modelos/TareaModelo.php';
$model = new TareaModelo($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$path = $_GET['action'] ?? '';
try {
    if($method==='GET' && $path==='list'){
        echo json_encode(['success'=>true,'data'=>$model->listar()]);
        exit;
    }
    if($method==='GET' && $path==='get' && isset($_GET['id'])){
        echo json_encode(['success'=>true,'data'=>$model->obtener((int)$_GET['id'])]);
        exit;
    }
    $input=json_decode(file_get_contents('php://input'),true)??$_POST;

    if($method==='POST' && $path==='create'){
        $titulo=trim($input['titulo']??'');
        if($titulo===''){http_response_code(422); echo json_encode(['success'=>false,'error'=>'El título es obligatorio']); exit;}
        $descripcion=$input['descripcion']??'';
        $estado=in_array($input['estado']??'pendiente',['pendiente','en revision','completada'])?$input['estado']:'pendiente';
        $fecha_limite=$input['fecha_limite']??null;
        $id=$model->crear($titulo,$descripcion,$estado,$fecha_limite);
        echo json_encode(['success'=>true,'id'=>$id]); exit;
    }

if(($method==='POST' || $method==='PUT') && $path==='update'){
    $id = (int)($input['id'] ?? 0);
    if(!$id){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'ID inválido']);
        exit;
    }

    $titulo = trim($input['titulo'] ?? '');
    $descripcion = trim($input['descripcion'] ?? '');
    $fecha_limite = trim($input['fecha_limite'] ?? '');

    if($titulo==='' || $descripcion==='' || $fecha_limite===''){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'Todos los campos son obligatorios']);
        exit;
    }

    // Obtener el estado actual para no modificarlo
    $tarea = $model->obtener($id);
    if(!$tarea){
        http_response_code(404);
        echo json_encode(['success'=>false,'error'=>'Tarea no encontrada']);
        exit;
    }
    $estadoActual = $tarea['estado'];

    $success = $model->actualizar($id, $titulo, $descripcion, $estadoActual, $fecha_limite);
    echo json_encode(['success'=>$success]);
    exit;
}


    if(($method==='DELETE'||$method==='POST') && $path==='delete'){
        $id=(int)($input['id']??$_GET['id']??0);
        if(!$id){http_response_code(422); echo json_encode(['success'=>false,'error'=>'ID inválido']); exit;}
        echo json_encode(['success'=>$model->eliminar($id)]);
        exit;
    }

    http_response_code(400);
    echo json_encode(['success'=>false,'error'=>'Ruta no encontrada.']);
}catch(Exception $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Error interno: '.$e->getMessage()]);
}
