<?php
class TareaModelo {
    private $pdo;
    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    public function crear($titulo, $descripcion, $estado, $fecha_limite) {
        $sql = "INSERT INTO tareas (titulo, descripcion, estado, fecha_limite) VALUES (:titulo,:descripcion,:estado,:fecha_limite)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':titulo'=>$titulo,':descripcion'=>$descripcion,':estado'=>$estado,':fecha_limite'=>$fecha_limite ?: null]);
        return $this->pdo->lastInsertId();
    }

    public function listar() {
        $sql = "SELECT * FROM tareas ORDER BY creado_en DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function obtener($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM tareas WHERE id=:id");
        $stmt->execute([':id'=>$id]);
        return $stmt->fetch();
    }

    public function actualizar($id,$titulo,$descripcion,$estado,$fecha_limite){
        $stmt=$this->pdo->prepare("UPDATE tareas SET titulo=:titulo,descripcion=:descripcion,estado=:estado,fecha_limite=:fecha_limite WHERE id=:id");
        return $stmt->execute([':titulo'=>$titulo,':descripcion'=>$descripcion,':estado'=>$estado,':fecha_limite'=>$fecha_limite ?: null,':id'=>$id]);
    }

    public function eliminar($id){
        $stmt=$this->pdo->prepare("DELETE FROM tareas WHERE id=:id");
        return $stmt->execute([':id'=>$id]);
    }
}
