<?php
abstract class Controller
{
    protected function render(string $view, array $dados = []): void
    {
        $dados['usuarioNome']   = $_SESSION['usuario_nome'] ?? '';
        $dados['usuarioPerfil'] = $_SESSION['usuario_perfil'] ?? '';
        extract($dados);
        require __DIR__ . '/../view/' . $view . '.php';
    }

    protected function redirecionar(string $acao): void
    {
        header("Location: index.php?acao=" . $acao);
        exit;
    }

    protected function exigirLogin(): void
    {
        if (!isset($_SESSION['usuario_id'])) {
            $_SESSION['erros'] = ["Faça login para acessar esta página."];
            $this->redirecionar('login');
        }
    }

    protected function exigirPerfil(string $perfil): void
    {
        $this->exigirLogin();

        if ($_SESSION['usuario_perfil'] !== $perfil) {
            $_SESSION['erros'] = ["Você não tem permissão para acessar esta página."];
            $this->redirecionar('catalogo');
        }
    }
}
