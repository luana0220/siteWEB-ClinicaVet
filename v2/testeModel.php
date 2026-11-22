
<?php




spl_autoload_register(function ($classe) {
    $arquivo = __DIR__ . "/model/$classe.php";

    if (file_exists($arquivo)) {
        require $arquivo;
    }
});

require __DIR__ . '/config/conexao.php';

$servicoModel = new ServicoModel($pdo);
$categoriaModel = new CategoriaModel($pdo);

echo "<h2>Serviços cadastrados</h2>";

foreach ($servicoModel->listar() as $servico) {
    echo htmlspecialchars($servico['nome']);
    echo " - R$ " . number_format($servico['valor'], 2, ',', '.');
    echo "<br>";
}

echo "<h2>Categorias</h2>";

foreach ($categoriaModel->listar() as $categoria) {
    echo htmlspecialchars($categoria['nome']) . "<br>";
}

?>