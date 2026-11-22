<?php
class ServicoModel extends Model
{
    public function categorias(): array
    {
        $stmt = $this->pdo->prepare("SELECT id, nome FROM categorias ORDER BY nome ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function categoriaExiste(int $id_categoria): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM categorias WHERE id = :id");
        $stmt->execute(['id' => $id_categoria]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function listar(int $id_categoria = 0): array
    {
        $sql = "SELECT servicos.id, servicos.nome, servicos.id_categoria, servicos.imagem, servicos.valor,
                       servicos.descricao, categorias.nome AS categoria
                  FROM servicos
                  JOIN categorias ON categorias.id = servicos.id_categoria";

        if ($id_categoria === 0) {
            $stmt = $this->pdo->prepare($sql . " ORDER BY servicos.nome ASC");
            $stmt->execute();
        } else {
            $stmt = $this->pdo->prepare($sql . " WHERE servicos.id_categoria = :id_categoria ORDER BY servicos.nome ASC");
            $stmt->execute(['id_categoria' => $id_categoria]);
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id): array|false
    {
        $sql  = "SELECT servicos.id, servicos.nome, servicos.id_categoria, servicos.imagem, servicos.valor,
                        servicos.descricao, categorias.nome AS categoria
                   FROM servicos
                   JOIN categorias ON categorias.id = servicos.id_categoria
                  WHERE servicos.id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function validar(string $nome, int $id_categoria, string $valor, string $descricao): array
    {
        $erros = [];

        if ($nome === '' || $id_categoria === 0 || $valor === '' || $descricao === '') {
            $erros[] = "Nome, categoria, preço e descrição são obrigatórios.";
        }
        if (mb_strlen($nome) > 100) {
            $erros[] = "O nome deve ter no máximo 100 caracteres.";
        }
        if ($id_categoria !== 0 && !$this->categoriaExiste($id_categoria)) {
            $erros[] = "Categoria inválida.";
        }
        if ($valor !== '' && (!is_numeric($valor) || $valor <= 0)) {
            $erros[] = "O preço deve ser um número maior que zero.";
        }

        return $erros;
    }

    public function inserir(string $nome, int $id_categoria, string $valor, string $descricao, string $imagem): void
    {
        $sql  = "INSERT INTO servicos (nome, id_categoria, valor, descricao, imagem)
                 VALUES (:nome, :id_categoria, :valor, :descricao, :imagem)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome'         => $nome,
            'id_categoria' => $id_categoria,
            'valor'        => $valor,
            'descricao'    => $descricao,
            'imagem'       => $imagem
        ]);
    }

    public function atualizar(int $id, string $nome, int $id_categoria, string $valor, string $descricao, string $imagem): void
    {
        $sql  = "UPDATE servicos SET nome = :nome, id_categoria = :id_categoria, valor = :valor,
                        descricao = :descricao, imagem = :imagem
                  WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome'         => $nome,
            'id_categoria' => $id_categoria,
            'valor'        => $valor,
            'descricao'    => $descricao,
            'imagem'       => $imagem,
            'id'           => $id
        ]);
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM servicos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() === 1;
    }
}
