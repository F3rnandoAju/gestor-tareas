<?php
class TareaModelo {
    private $pdo;

    // ================== CONSTRUCTOR ==================
    // Guardo la conexión PDO al crear el modelo
    // (la conexión se pasa desde fuera)
    public function __construct(PDO $pdo) { 
        $this->pdo = $pdo; 
    }

    // ================== CREAR ==================
    // Inserta una nueva tarea en la BD
    // - titulo, descripcion, estado, fecha_limite
    // - si fecha_limite viene vacío → se guarda NULL
    // Devuelve el id recién insertado
    public function crear($titulo, $descripcion, $estado, $fecha_limite) {
        $sql = "INSERT INTO tareas (titulo, descripcion, estado, fecha_limite) 
                VALUES (:titulo,:descripcion,:estado,:fecha_limite)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':titulo'       => $titulo,
            ':descripcion'  => $descripcion,
            ':estado'       => $estado,
            ':fecha_limite' => $fecha_limite ?: null
        ]);
        return $this->pdo->lastInsertId();
    }

    // ================== LISTAR ==================
    // Devuelve todas las tareas, ordenadas de la más nueva a la más vieja
    public function listar() {
        $sql = "SELECT * FROM tareas ORDER BY creado_en DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    // ================== OBTENER ==================
    // Busca UNA tarea por su id
    // Si no existe, devuelve false
    public function obtener($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tareas WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    // ================== ACTUALIZAR ==================
    // Modifica una tarea existente
    // Devuelve true/false según si se pudo actualizar
    public function actualizar($id,$titulo,$descripcion,$estado,$fecha_limite){
        $stmt=$this->pdo->prepare("
            UPDATE tareas 
            SET titulo=:titulo,
                descripcion=:descripcion,
                estado=:estado,
                fecha_limite=:fecha_limite 
            WHERE id=:id
        ");
        return $stmt->execute([
            ':titulo'       => $titulo,
            ':descripcion'  => $descripcion,
            ':estado'       => $estado,
            ':fecha_limite' => $fecha_limite ?: null,
            ':id'           => $id
        ]);
    }

    // ================== ELIMINAR ==================
    // Borra la tarea por id
    // Devuelve true/false según si se borró
    public function eliminar($id){
        $stmt=$this->pdo->prepare("DELETE FROM tareas WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
