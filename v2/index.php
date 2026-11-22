<?php
// Único ponto de entrada: todo pedido chega aqui como index.php?acao=...
session_start();

// Autoload: carrega a classe só quando ela é usada (sem require_once de classes)
spl_autoload_register(function (string $classe) {
    $classe = basename(str_replace('\\', '/', $classe));
    foreach (['controller', 'model'] as $pasta) {
        $arquivo = __DIR__ . '/' . $pasta . '/' . $classe . '.php';
        if (is_file($arquivo)) {
            require $arquivo;
            return;
        }
    }
});

require __DIR__ . '/config/conexao.php'; // precisa definir a variável $pdo

// Tabela de rotas: 'acao' => [Controller, método]
$rotas = [
    // Visitante
    'catalogo'              => ['ServicoController', 'catalogo'],
    'detalhes'              => ['ServicoController', 'detalhes'],
    'login'                 => ['UsuarioController', 'formLogin'],
    'entrar'                => ['UsuarioController', 'login'],
    'cadastro'              => ['UsuarioController', 'formCadastro'],
    'cadastrar'             => ['UsuarioController', 'cadastrar'],

    // Cliente
    'sair'                  => ['UsuarioController', 'logout'],
    'nova_solicitacao'      => ['SolicitacaoController', 'formulario'],
    'salvar_solicitacao'    => ['SolicitacaoController', 'salvar'],
    'minhas_solicitacoes'   => ['SolicitacaoController', 'minhas'],
    'cancelar_solicitacao'  => ['SolicitacaoController', 'cancelar'],

    // Administrador
    'admin_servicos'        => ['ServicoController', 'listarAdmin'],
    'admin_servico_novo'    => ['ServicoController', 'formNovo'],
    'admin_servico_salvar'  => ['ServicoController', 'salvar'],
    'admin_servico_editar'  => ['ServicoController', 'formEditar'],
    'admin_servico_atualizar' => ['ServicoController', 'atualizar'],
    'admin_servico_excluir' => ['ServicoController', 'excluir'],
];

$acao = $_GET['acao'] ?? 'catalogo';

[$classe, $metodo] = $rotas[$acao] ?? ['ErroController', 'naoEncontrado'];

// Se a rota existe mas o controller/método ainda não foi escrito, responde 404
if (!class_exists($classe) || !method_exists($classe, $metodo)) {
    [$classe, $metodo] = ['ErroController', 'naoEncontrado'];
}

$controller = new $classe($pdo);
$controller->$metodo();