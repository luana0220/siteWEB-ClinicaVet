
<?php

class ServicoModel extends Model
{
    public function listar(): array
    {
        $sql = "SELECT s.*, c.nome AS categoria
                FROM servicos s
                INNER JOIN categorias c
                    ON c.id = s.id_categoria
                ORDER BY s.id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function buscarPorId(int $id): array|false
    {
        $sql = "SELECT s.*, c.nome AS categoria
                FROM servicos s
                INNER JOIN categorias c
                    ON c.id = s.id_categoria
                WHERE s.id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch();
    }

    public function cadastrar(
        string $nome,
        int $categoria,
        ?string $imagem,
        float $valor,
        string $descricao
    ): bool {
        $sql = "INSERT INTO servicos
                (nome, id_categoria, imagem, valor, descricao)
                VALUES
                (:nome, :categoria, :imagem, :valor, :descricao)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'nome' => $nome,
            'categoria' => $categoria,
            'imagem' => $imagem,
            'valor' => $valor,
            'descricao' => $descricao
        ]);
    }

    public function atualizar(
        int $id,
        string $nome,
        int $categoria,
        ?string $imagem,
        float $valor,
        string $descricao
    ): bool {
        $sql = "UPDATE servicos
                SET nome = :nome,
                    id_categoria = :categoria,
                    imagem = :imagem,
                    valor = :valor,
                    descricao = :descricao
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'id' => $id,
            'nome' => $nome,
            'categoria' => $categoria,
            'imagem' => $imagem,
            'valor' => $valor,
            'descricao' => $descricao
        ]);
    }

    public function excluir(int $id): bool
    {
        $sql = "DELETE FROM servicos WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id' => $id]);
    }
}
?>