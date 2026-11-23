<?php
class ServicoController extends Controller
{
    private ServicoModel $servicoModel;

    private array $cores = [
        'Consultas'     => 'bg-info',
        'Vacinação'     => 'bg-success',
        'Procedimentos' => 'bg-danger',
        'Higiene'       => 'bg-primary',
        'Exames'        => 'bg-secondary',
    ];

    public function __construct(PDO $pdo)
    {
        $this->servicoModel = new ServicoModel($pdo);
    }

    private function preparar(array $servico): array
    {
        $servico['valor_formatado'] = number_format($servico['valor'], 2, ',', '.');
        $servico['imagem']          = $servico['imagem'] ?? 'default.png';
        $servico['cor']             = $this->cores[$servico['categoria']] ?? 'bg-dark';
        return $servico;
    }

    public function catalogo(): void
    {
        $erros   = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso']);

        $categoria = filter_input(INPUT_GET, 'categoria', FILTER_VALIDATE_INT);
        if (!$categoria) {
            $categoria = 0;
        }

        $categorias = [];
        $servicos   = [];
        try {
            $categorias = $this->servicoModel->categorias();
            foreach ($this->servicoModel->listar($categoria) as $servico) {
                $servicos[] = $this->preparar($servico);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros[] = "Não foi possível carregar os serviços.";
        }

        $this->render('catalogo_view', [
            'servicos'   => $servicos,
            'categorias' => $categorias,
            'categoria'  => $categoria,
            'erros'      => $erros,
            'sucesso'    => $sucesso,
        ]);
    }

    public function detalhes(): void
    {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $servico = false;
        if ($id) {
            try {
                $servico = $this->servicoModel->buscar($id);
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }

        if ($servico === false) {
            $_SESSION['erros'] = ["Serviço não encontrado."];
            $this->redirecionar('catalogo');
        }

        $this->render('detalhes_view', [
            'servico' => $this->preparar($servico),
        ]);
    }

    public function painel(): void
    {
        $this->exigirPerfil('administrador');

        $erros   = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso']);

        $servicos = [];
        try {
            foreach ($this->servicoModel->listar() as $servico) {
                $servicos[] = $this->preparar($servico);
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros[] = "Não foi possível carregar os serviços.";
        }

        $this->render('admin_view', [
            'servicos' => $servicos,
            'erros'    => $erros,
            'sucesso'  => $sucesso,
        ]);
    }

    private function categorias(): array
    {
        try {
            return $this->servicoModel->categorias();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function novo(): void
    {
        $this->exigirPerfil('administrador');

        $erros = $_SESSION['erros'] ?? [];
        $dados = $_SESSION['dados_servico'] ?? [];
        unset($_SESSION['erros'], $_SESSION['dados_servico']);

        $this->render('servico_form_view', [
            'titulo'     => 'Novo Serviço',
            'id'         => 0,
            'dados'      => $dados,
            'categorias' => $this->categorias(),
            'erros'      => $erros,
            'sucesso'    => '',
        ]);
    }

    public function editar(): void
    {
        $this->exigirPerfil('administrador');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $servico = false;
        if ($id) {
            try {
                $servico = $this->servicoModel->buscar($id);
            } catch (PDOException $e) {
                error_log($e->getMessage());
            }
        }

        if ($servico === false) {
            $_SESSION['erros'] = ["Serviço não encontrado."];
            $this->redirecionar('admin');
        }

        $erros = $_SESSION['erros'] ?? [];
        $dados = $_SESSION['dados_servico'] ?? $servico;
        unset($_SESSION['erros'], $_SESSION['dados_servico']);

        $this->render('servico_form_view', [
            'titulo'     => 'Editar Serviço #' . $id,
            'id'         => $id,
            'dados'      => $dados,
            'categorias' => $this->categorias(),
            'erros'      => $erros,
            'sucesso'    => '',
        ]);
    }

    public function salvar(): void
    {
        $this->exigirPerfil('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('admin');
        }

        $id           = (int) ($_POST['id'] ?? 0);
        $nome         = trim($_POST['nome'] ?? '');
        $id_categoria = (int) ($_POST['id_categoria'] ?? 0);
        $valor        = trim($_POST['valor'] ?? '');
        $descricao    = trim($_POST['descricao'] ?? '');
        $imagem       = trim($_POST['imagem'] ?? '');

        if ($imagem === '') {
            $imagem = 'default.png';
        }

        try {
            $erros = $this->servicoModel->validar($nome, $id_categoria, $valor, $descricao);

            if (empty($erros)) {
                if ($id > 0) {
                    $this->servicoModel->atualizar($id, $nome, $id_categoria, $valor, $descricao, $imagem);
                    $_SESSION['mensagem_sucesso'] = "Serviço atualizado com sucesso!";
                } else {
                    $this->servicoModel->inserir($nome, $id_categoria, $valor, $descricao, $imagem);
                    $_SESSION['mensagem_sucesso'] = "Serviço cadastrado com sucesso!";
                }
                $this->redirecionar('admin');
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $erros = ["Erro ao salvar o serviço."];
        }

        $_SESSION['erros']         = $erros;
        $_SESSION['dados_servico'] = [
            'nome'         => $nome,
            'id_categoria' => $id_categoria,
            'valor'        => $valor,
            'descricao'    => $descricao,
            'imagem'       => $imagem,
        ];

        if ($id > 0) {
            $this->redirecionar('editarServico&id=' . $id);
        }
        $this->redirecionar('novoServico');
    }

    public function excluir(): void
    {
        $this->exigirPerfil('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('admin');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            $_SESSION['erros'] = ["Serviço inválido."];
            $this->redirecionar('admin');
        }

        try {
            if ($this->servicoModel->excluir($id)) {
                $_SESSION['mensagem_sucesso'] = "Serviço excluído com sucesso!";
            } else {
                $_SESSION['erros'] = ["Serviço não encontrado."];
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $_SESSION['erros'] = ["Não foi possível excluir: o serviço possui solicitações."];
        }

        $this->redirecionar('admin');
    }
}
