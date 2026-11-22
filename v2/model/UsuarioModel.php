<?php
class UsuarioModel extends Model
{
    public function autenticar(string $email, string $senha): array|false
    {
        $stmt = $this->pdo->prepare("SELECT id, nome, perfil, senha FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return ['id' => $usuario['id'], 'nome' => $usuario['nome'], 'perfil' => $usuario['perfil']];
        }
        return false;
    }

    public function emailExiste(string $email): bool
    {
        $stmt = $this->pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC) !== false;
    }

    public function validar(string $nome, string $email, string $telefone, string $senha, string $confirmaSenha): array
    {
        $erros = [];

        if ($nome === '' || $email === '' || $telefone === '' || $senha === '') {
            $erros[] = "Todos os campos são obrigatórios.";
        }
        if (mb_strlen($nome) > 100) {
            $erros[] = "O nome deve ter no máximo 100 caracteres.";
        }
        if (mb_strlen($telefone) > 20) {
            $erros[] = "O telefone deve ter no máximo 20 caracteres.";
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erros[] = "Informe um e-mail válido.";
        }
        if (strlen($senha) < 8) {
            $erros[] = "A senha deve ter no mínimo 8 caracteres.";
        }
        if ($senha !== $confirmaSenha) {
            $erros[] = "A confirmação de senha não confere.";
        }
        if (empty($erros) && $this->emailExiste($email)) {
            $erros[] = "Este e-mail já está cadastrado.";
        }

        return $erros;
    }

    public function inserir(string $nome, string $email, string $telefone, string $senha): void
    {
        $sql  = "INSERT INTO usuarios (nome, email, telefone, senha) VALUES (:nome, :email, :telefone, :senha)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nome'     => $nome,
            'email'    => $email,
            'telefone' => $telefone,
            'senha'    => password_hash($senha, PASSWORD_DEFAULT)
        ]);
    }
}
