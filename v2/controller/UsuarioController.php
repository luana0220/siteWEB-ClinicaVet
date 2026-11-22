<?php
class UsuarioController extends Controller
{
    private UsuarioModel $usuarioModel;

    public function __construct(PDO $pdo)
    {
        $this->usuarioModel = new UsuarioModel($pdo);
    }

    public function login(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirecionar('catalogo');
        }

        $erros   = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso']);

        $this->render('login_view', [
            'erros'   => $erros,
            'sucesso' => $sucesso,
            'email'   => $_COOKIE['lembrar_email'] ?? '',
        ]);
    }

    public function autenticar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('login');
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if ($email && $senha !== '') {
            try {
                $usuario = $this->usuarioModel->autenticar($email, $senha);

                if ($usuario !== false) {
                    session_regenerate_id(true);

                    $_SESSION['usuario_id']     = $usuario['id'];
                    $_SESSION['usuario_nome']   = $usuario['nome'];
                    $_SESSION['usuario_perfil'] = $usuario['perfil'];

                    if (isset($_POST['lembrar'])) {
                        setcookie('lembrar_email', $email, time() + 30 * 24 * 60 * 60, "/");
                    } else {
                        setcookie('lembrar_email', '', time() - 3600, "/");
                    }

                    if ($usuario['perfil'] === 'administrador') {
                        $this->redirecionar('admin');
                    }
                    $this->redirecionar('catalogo');
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }

        $_SESSION['erros'] = ["E-mail ou senha incorretos."];
        $this->redirecionar('login');
    }

    public function cadastro(): void
    {
        if (isset($_SESSION['usuario_id'])) {
            $this->redirecionar('catalogo');
        }

        $erros = $_SESSION['erros'] ?? [];
        $dados = $_SESSION['dados_cadastro'] ?? [];
        unset($_SESSION['erros'], $_SESSION['dados_cadastro']);

        $this->render('cadastro_view', [
            'erros'   => $erros,
            'sucesso' => '',
            'dados'   => $dados,
        ]);
    }

    public function cadastrar(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('cadastro');
        }

        $nome          = trim($_POST['nome'] ?? '');
        $email         = trim($_POST['email'] ?? '');
        $telefone      = trim($_POST['telefone'] ?? '');
        $senha         = $_POST['senha'] ?? '';
        $confirmaSenha = $_POST['confirma_senha'] ?? '';

        try {
            $erros = $this->usuarioModel->validar($nome, $email, $telefone, $senha, $confirmaSenha);

            if (empty($erros)) {
                $this->usuarioModel->inserir($nome, $email, $telefone, $senha);

                $_SESSION['mensagem_sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
                $this->redirecionar('login');
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros = ["Erro ao realizar o cadastro."];
        }

        $_SESSION['erros']          = $erros;
        $_SESSION['dados_cadastro'] = ['nome' => $nome, 'email' => $email, 'telefone' => $telefone];
        $this->redirecionar('cadastro');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        $this->redirecionar('catalogo');
    }
}
