
<?php

class CategoriaModel extends Model
{
    public function listar(): array
    {
        $sql = "SELECT id, nome
                FROM categorias
                ORDER BY id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
?>