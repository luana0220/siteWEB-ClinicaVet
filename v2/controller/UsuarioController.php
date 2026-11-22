<?php
class UsuarioController extends Controller
{
    private UsuarioModel $usuarios;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->usuarios = new UsuarioModel($pdo);
    }

    // GET index.php?acao=login
    public function formLogin(): void
    {
        $this->irParaInicioSeLogado();
        $this->render('login_view');
    }

    // POST index.php?acao=entrar
    public function login(): void
    {
        $this->exigirPost();

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->usuarios->buscarPorEmail($email);

        // Mesma mensagem para e-mail inexistente e senha errada
        if (!$usuario || !password_verify($senha, $usuario['senha'])) {
            $this->definirMensagem('E-mail ou senha incorretos.', 'danger');
            $this->redirecionar('login');
        }

        session_regenerate_id(true); // evita fixação de sessão
        $_SESSION['usuario_id']     = $usuario['id'];
        $_SESSION['usuario_nome']   = $usuario['nome'];
        $_SESSION['usuario_perfil'] = $usuario['perfil'];

        $this->definirMensagem('Bem-vindo(a), ' . $usuario['nome'] . '!');
        $this->redirecionar($this->paginaInicial($usuario['perfil']));
    }

    // GET index.php?acao=cadastro
    public function formCadastro(): void
    {
        $this->irParaInicioSeLogado();
        $this->render('cadastro_view');
    }

    // POST index.php?acao=cadastrar
    public function cadastrar(): void
    {
        $this->exigirPost();

        $nome      = trim($_POST['nome'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $senha     = $_POST['senha'] ?? '';
        $confirmar = $_POST['confirmar_senha'] ?? '';

        $erro = null;
        if ($nome === '') {
            $erro = 'Informe seu nome.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $erro = 'Informe um e-mail válido.';
        } elseif (strlen($senha) < 6) {
            $erro = 'A senha precisa ter pelo menos 6 caracteres.';
        } elseif ($senha !== $confirmar) {
            $erro = 'A confirmação de senha não confere.';
        } elseif ($this->usuarios->buscarPorEmail($email)) {
            $erro = 'Já existe uma conta com esse e-mail.';
        }

        if ($erro !== null) {
            $this->definirMensagem($erro, 'danger');
            $this->redirecionar('cadastro');
        }

        // O perfil é fixo: quem se cadastra pelo site é sempre cliente
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $this->usuarios->criar($nome, $email, $hash, 'cliente');

        $this->definirMensagem('Cadastro realizado! Faça login para continuar.');
        $this->redirecionar('login');
    }

    // POST index.php?acao=sair
    public function logout(): void
    {
        $this->exigirPost();

        $_SESSION = [];
        session_regenerate_id(true);

        $this->definirMensagem('Você saiu do sistema.');
        $this->redirecionar('catalogo');
    }

    // ---------- auxiliares ----------
    private function paginaInicial(string $perfil): string
    {
        return $perfil === 'admin' ? 'admin_servicos' : 'catalogo';
    }

    private function irParaInicioSeLogado(): void
    {
        $usuario = $this->usuarioLogado();
        if ($usuario !== null) {
            $this->redirecionar($this->paginaInicial($usuario['perfil']));
        }
    }
}
