<?php
session_start();

require_once __DIR__ . '/config/conexao.php';

spl_autoload_register(function (string $classe): void {
    foreach (['model', 'controller'] as $pasta) {
        $arquivo = __DIR__ . "/$pasta/$classe.php";
        if (file_exists($arquivo)) {
            require $arquivo;
            return;
        }
    }
});

function paginaNaoEncontrada(): void
{
    http_response_code(404);
    echo "Página não encontrada.";
}

$rotas = [
    'catalogo'       => [ServicoController::class, 'catalogo'],
    'detalhes'       => [ServicoController::class, 'detalhes'],
    'admin'          => [ServicoController::class, 'painel'],
    'novoServico'    => [ServicoController::class, 'novo'],
    'editarServico'  => [ServicoController::class, 'editar'],
    'salvarServico'  => [ServicoController::class, 'salvar'],
    'excluirServico' => [ServicoController::class, 'excluir'],
    'login'          => [UsuarioController::class, 'login'],
    'autenticar'     => [UsuarioController::class, 'autenticar'],
    'cadastro'       => [UsuarioController::class, 'cadastro'],
    'cadastrar'      => [UsuarioController::class, 'cadastrar'],
    'sair'           => [UsuarioController::class, 'logout'],
    'solicitar'      => [SolicitacaoController::class, 'nova'],
    'adicionar'      => [SolicitacaoController::class, 'adicionar'],
    'solicitacao'    => [SolicitacaoController::class, 'atual'],
    'remover'        => [SolicitacaoController::class, 'remover'],
    'agendar'        => [SolicitacaoController::class, 'criar'],
    'minhas'         => [SolicitacaoController::class, 'minhas'],
    'cancelarItem'   => [SolicitacaoController::class, 'cancelarItem'],
    'cancelar'       => [SolicitacaoController::class, 'cancelar'],
    'solicitacoes'   => [SolicitacaoController::class, 'todas'],
    'confirmar'      => [SolicitacaoController::class, 'confirmar'],
];

$acao = $_GET['acao'] ?? 'catalogo';

if (!isset($rotas[$acao])) {
    paginaNaoEncontrada();
    exit;
}

[$classe, $metodo] = $rotas[$acao];
$controller = new $classe($pdo);
$controller->$metodo();
