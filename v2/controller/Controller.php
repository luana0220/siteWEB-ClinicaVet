<?php
abstract class Controller
{
    public function __construct(protected PDO $pdo)
    {
    }

    // Mostra uma view. Toda view recebe também $mensagem e $usuario.
    protected function render(string $view, array $dados = []): void
    {
        $dados['mensagem'] = $this->pegarMensagem();
        $dados['usuario']  = $this->usuarioLogado();
        extract($dados);
        require __DIR__ . '/../view/' . $view . '.php';
    }

    // Redireciona para outra rota (padrão POST -> redirect -> GET)
    protected function redirecionar(string $acao, array $params = []): void
    {
        $url = 'index.php?acao=' . urlencode($acao);
        if (!empty($params)) {
            $url .= '&' . http_build_query($params);
        }
        header('Location: ' . $url);
        exit;
    }

    // ---------- Mensagens (passam pela sessão) ----------
    protected function definirMensagem(string $texto, string $tipo = 'success'): void
    {
        $_SESSION['mensagem'] = ['texto' => $texto, 'tipo' => $tipo];
    }

    private function pegarMensagem(): ?array
    {
        $mensagem = $_SESSION['mensagem'] ?? null;
        unset($_SESSION['mensagem']);
        return $mensagem;
    }

    // ---------- Sessão e controle de acesso ----------
    protected function usuarioLogado(): ?array
    {
        if (!isset($_SESSION['usuario_id'])) {
            return null;
        }
        return [
            'id'     => $_SESSION['usuario_id'],
            'nome'   => $_SESSION['usuario_nome'],
            'perfil' => $_SESSION['usuario_perfil'],
        ];
    }

    // O dono dos dados vem SEMPRE daqui, nunca do formulário
    protected function usuarioId(): int
    {
        return (int) ($_SESSION['usuario_id'] ?? 0);
    }

    protected function exigirLogin(): void
    {
        if ($this->usuarioLogado() === null) {
            $this->definirMensagem('Faça login para continuar.', 'warning');
            $this->redirecionar('login');
        }
    }

    protected function exigirPerfil(string $perfil): void
    {
        $this->exigirLogin();
        if ($_SESSION['usuario_perfil'] !== $perfil) {
            $this->definirMensagem('Você não tem permissão para acessar essa página.', 'danger');
            $this->redirecionar('catalogo');
        }
    }

    // Alterações (cadastrar, editar, excluir, cancelar) só por POST
    protected function exigirPost(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            header('Allow: POST');
            exit('Método não permitido.');
        }
    }
}