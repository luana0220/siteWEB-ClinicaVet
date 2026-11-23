<?php
class SolicitacaoController extends Controller
{
    private SolicitacaoModel $solicitacaoModel;
    private ServicoModel $servicoModel;

    private array $cores = [
        'pendente' => 'bg-warning text-dark',
        'confirmada' => 'bg-success',
        'cancelada' => 'bg-secondary',
    ];

    public function __construct(PDO $pdo)
    {
        $this->solicitacaoModel = new SolicitacaoModel($pdo);
        $this->servicoModel = new ServicoModel($pdo);
    }

    private function agrupar(array $linhas): array
    {
        $sexos = $this->solicitacaoModel->sexos();
        $solicitacoes = [];

        foreach ($linhas as $linha) {
            $id = $linha['solicitacao_id'];

            if (!isset($solicitacoes[$id])) {
                $solicitacoes[$id] = [
                    'id' => $id,
                    'cliente' => $linha['cliente'],
                    'telefone' => $linha['telefone'],
                    'status' => $linha['status_solicitacao'],
                    'cor' => $this->cores[$linha['status_solicitacao']] ?? 'bg-dark',
                    'data' => date('d/m/Y H:i', strtotime($linha['data_solicitacao'])),
                    'total' => number_format($linha['valor_total'], 2, ',', '.'),
                    'itens' => [],
                ];
            }

            $cancelado = '';
            if ($linha['cancelado_em'] !== null) {
                $cancelado = date('d/m/Y H:i', strtotime($linha['cancelado_em']));
            }

            $solicitacoes[$id]['itens'][] = [
                'id' => $linha['item_id'],
                'animal' => $linha['animal'],
                'especie' => $linha['especie'],
                'sexo' => $sexos[$linha['sexo']] ?? $linha['sexo'],
                'idade' => $linha['idade'],
                'servico' => $linha['servico'],
                'data' => date('d/m/Y', strtotime($linha['data_marcada'])),
                'horario' => substr($linha['horario'], 0, 5),
                'valor' => number_format($linha['valor'], 2, ',', '.'),
                'status' => $linha['status_item'],
                'cor' => $this->cores[$linha['status_item']] ?? 'bg-dark',
                'cancelado' => $cancelado,
            ];
        }

        return $solicitacoes;
    }

    public function nova(): void
    {
        $this->exigirPerfil('cliente');

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        $servico = false;
        if ($id) {
            try {
                $servico = $this->servicoModel->buscar($id);
            } catch (RuntimeException $e) {
                error_log($e->getMessage());
            }
        }

        if ($servico === false) {
            $_SESSION['erros'] = ["Serviço não encontrado."];
            $this->redirecionar('catalogo');
        }

        $erros = $_SESSION['erros'] ?? [];
        $dados = $_SESSION['dados_atendimento'] ?? [];
        unset($_SESSION['erros'], $_SESSION['dados_atendimento']);

        $servico['valor_formatado'] = number_format($servico['valor'], 2, ',', '.');

        $this->render('solicitacao_form_view', [
            'servico' => $servico,
            'horarios' => $this->solicitacaoModel->horarios(),
            'sexos' => $this->solicitacaoModel->sexos(),
            'hoje' => date('Y-m-d'),
            'dados' => $dados,
            'erros' => $erros,
            'sucesso' => '',
        ]);
    }

    public function adicionar(): void
    {
        $this->exigirPerfil('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('catalogo');
        }

        $servico_id = filter_input(INPUT_POST, 'servico_id', FILTER_VALIDATE_INT);
        $nome = trim($_POST['nome'] ?? '');
        $especie = trim($_POST['especie'] ?? '');
        $sexo = trim($_POST['sexo'] ?? '');
        $idade = trim($_POST['idade'] ?? '');
        $data = trim($_POST['data'] ?? '');
        $horario = trim($_POST['horario'] ?? '');
        $escolhidos = $_SESSION['atendimentos'] ?? [];

        if ($servico_id === false || $servico_id === null || $servico_id <= 0) {
            $_SESSION['erros'] = ["Serviço inválido."];
            $this->redirecionar('catalogo');
        }

        try {
            $servico = $this->servicoModel->buscar($servico_id);

            if ($servico === false) {
                $_SESSION['erros'] = ["Serviço não encontrado."];
                $this->redirecionar('catalogo');
            }

            $erros = $this->solicitacaoModel->validarAtendimento($servico_id, $nome, $especie, $sexo, $idade, $data, $horario, $escolhidos);

            if (empty($erros)) {
                $_SESSION['atendimentos'][] = [
                    'servico_id' => $servico_id,
                    'servico' => $servico['nome'],
                    'valor' => $servico['valor'],
                    'nome' => $nome,
                    'especie' => $especie,
                    'sexo' => $sexo,
                    'idade' => (int) $idade,
                    'data' => $data,
                    'horario' => $horario,
                ];

                $_SESSION['mensagem_sucesso'] = "Atendimento adicionado à solicitação.";
                $this->redirecionar('solicitacao');
            }
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $erros = ["Não foi possível adicionar o atendimento."];
        }

        $_SESSION['erros'] = $erros;
        $_SESSION['dados_atendimento'] = [
            'nome' => $nome,
            'especie' => $especie,
            'sexo' => $sexo,
            'idade' => $idade,
            'data' => $data,
            'horario' => $horario,
        ];
        $this->redirecionar('solicitar&id=' . $servico_id);
    }

    public function atual(): void
    {
        $this->exigirPerfil('cliente');

        $erros = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        $telefone = $_SESSION['dados_telefone'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso'], $_SESSION['dados_telefone']);

        $sexos = $this->solicitacaoModel->sexos();
        $atendimentos = [];
        $total = 0;

        foreach ($_SESSION['atendimentos'] ?? [] as $indice => $atendimento) {
            $total += $atendimento['valor'];

            $atendimento['indice'] = $indice;
            $atendimento['sexo'] = $sexos[$atendimento['sexo']] ?? $atendimento['sexo'];
            $atendimento['data_formatada'] = date('d/m/Y', strtotime($atendimento['data']));
            $atendimento['valor_formatado'] = number_format($atendimento['valor'], 2, ',', '.');
            $atendimentos[] = $atendimento;
        }

        $this->render('solicitacao_view', [
            'atendimentos' => $atendimentos,
            'total' => number_format($total, 2, ',', '.'),
            'telefone' => $telefone,
            'erros' => $erros,
            'sucesso' => $sucesso,
        ]);
    }

    public function remover(): void
    {
        $this->exigirPerfil('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('solicitacao');
        }

        $indice = filter_input(INPUT_POST, 'indice', FILTER_VALIDATE_INT);

        if ($indice !== false && $indice !== null && isset($_SESSION['atendimentos'][$indice])) {
            unset($_SESSION['atendimentos'][$indice]);
            $_SESSION['atendimentos'] = array_values($_SESSION['atendimentos']);
            $_SESSION['mensagem_sucesso'] = "Atendimento removido da solicitação.";
        } else {
            $_SESSION['erros'] = ["Atendimento não encontrado."];
        }

        $this->redirecionar('solicitacao');
    }

    public function criar(): void
    {
        $this->exigirPerfil('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('solicitacao');
        }

        $telefone = trim($_POST['telefone'] ?? '');
        $atendimentos = $_SESSION['atendimentos'] ?? [];

        try {
            $erros = $this->solicitacaoModel->validarSolicitacao($telefone, $atendimentos);

            if (empty($erros)) {
                $this->solicitacaoModel->inserir($_SESSION['usuario_id'], $telefone, $atendimentos);

                unset($_SESSION['atendimentos']);
                $_SESSION['mensagem_sucesso'] = "Solicitação realizada com sucesso!";
                $this->redirecionar('minhas');
            }
        } catch (InvalidArgumentException $e) {
            $erros = [$e->getMessage()];
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $erros = ["Não foi possível realizar a solicitação. Verifique os horários escolhidos."];
        }

        $_SESSION['erros'] = $erros;
        $_SESSION['dados_telefone'] = $telefone;
        $this->redirecionar('solicitacao');
    }

    public function minhas(): void
    {
        $this->exigirPerfil('cliente');

        $erros = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso']);

        $solicitacoes = [];
        try {
            $solicitacoes = $this->agrupar($this->solicitacaoModel->listarDoUsuario($_SESSION['usuario_id']));
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $erros[] = "Não foi possível carregar as solicitações.";
        }

        $this->render('minhas_view', [
            'solicitacoes' => $solicitacoes,
            'erros' => $erros,
            'sucesso' => $sucesso,
        ]);
    }

    public function cancelarItem(): void
    {
        $this->exigirPerfil('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('minhas');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            $_SESSION['erros'] = ["Atendimento inválido."];
            $this->redirecionar('minhas');
        }

        try {
            if ($this->solicitacaoModel->cancelarItem($_SESSION['usuario_id'], $id)) {
                $_SESSION['mensagem_sucesso'] = "Atendimento cancelado e horário liberado.";
            } else {
                $_SESSION['erros'] = ["Operação não permitida ou atendimento não encontrado."];
            }
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $_SESSION['erros'] = ["Erro ao cancelar o atendimento."];
        }

        $this->redirecionar('minhas');
    }

    public function cancelar(): void
    {
        $this->exigirPerfil('cliente');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('minhas');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            $_SESSION['erros'] = ["Solicitação inválida."];
            $this->redirecionar('minhas');
        }

        try {
            if ($this->solicitacaoModel->cancelar($_SESSION['usuario_id'], $id)) {
                $_SESSION['mensagem_sucesso'] = "Solicitação cancelada e horários liberados.";
            } else {
                $_SESSION['erros'] = ["Operação não permitida ou solicitação não encontrada."];
            }
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $_SESSION['erros'] = ["Erro ao cancelar a solicitação."];
        }

        $this->redirecionar('minhas');
    }

    public function todas(): void
    {
        $this->exigirPerfil('administrador');

        $erros = $_SESSION['erros'] ?? [];
        $sucesso = $_SESSION['mensagem_sucesso'] ?? '';
        unset($_SESSION['erros'], $_SESSION['mensagem_sucesso']);

        $solicitacoes = [];
        try {
            $solicitacoes = $this->agrupar($this->solicitacaoModel->listarTodas());
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $erros[] = "Não foi possível carregar as solicitações.";
        }

        $this->render('solicitacoes_view', [
            'solicitacoes' => $solicitacoes,
            'erros' => $erros,
            'sucesso' => $sucesso,
        ]);
    }

    public function confirmar(): void
    {
        $this->exigirPerfil('administrador');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirecionar('solicitacoes');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

        if ($id === false || $id === null) {
            $_SESSION['erros'] = ["Atendimento inválido."];
            $this->redirecionar('solicitacoes');
        }

        try {
            if ($this->solicitacaoModel->confirmarItem($id)) {
                $_SESSION['mensagem_sucesso'] = "Atendimento confirmado.";
            } else {
                $_SESSION['erros'] = ["Atendimento não encontrado ou já confirmado ou cancelado."];
            }
        } catch (RuntimeException $e) {
            error_log($e->getMessage());
            $_SESSION['erros'] = ["Erro ao confirmar o atendimento."];
        }

        $this->redirecionar('solicitacoes');
    }
}
?>