<?php
require "dados.php";


$filtroPorCategoria = $_GET['categoria'] ?? 'Todos';



function spanEstoque($item)
{
    $numHorDisponiveis = count(($item['horarios']));
    if ($numHorDisponiveis == 0) {
        return "<span class= 'badge text-bg-danger'>" . $numHorDisponiveis . " disponíveis</span>";
    } else if ($numHorDisponiveis <= 3) {
        return "<span class= 'badge text-bg-warning'>" . $numHorDisponiveis . " disponíveis</span>";
    } else {
        return "<span class= 'badge text-bg-success'>" . $numHorDisponiveis . " disponíveis</span>";
    }
}


$itensFiltrados = array_filter($itens, function ($item) use ($filtroPorCategoria) {
    return $filtroPorCategoria == "Todos" || $filtroPorCategoria == $item['categoria'];
});
$numServicos = count($itensFiltrados);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Clínica ANIMED</title>

</head>

<body>
<?php require "header.php"; ?>
    <body>
        <div class="container mt-4">
            <div class="card mb-4">
                 <h2 class="card-title m-3">Serviços Disponíveis da clínica</h2>
                <div class="card-body">
                    <h4 class="card-text">Quantidade de serviçoes disponíveis atualmente: <?= $numServicos ?></h4>
                    <!-- Categorias -->
                    <div class="list-group list-group-horizontal justify-content-end mb-4 gap-1">
                        <?php foreach ($categorias as $categoria): ?>
                            <a href="?categoria=<?= $categoria ?>"
                                class="list-group-item list-group-item-action fw-bold text-center ?> <?= $filtroPorCategoria == $categoria ? 'active' : '' ?>">
                                <?= htmlspecialchars($categoria) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>


            <div class="row text-center">
                <?php foreach ($itensFiltrados as $item): ?>

                    <div class="col-sm-6 col-md-4 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header text-center text-white fw-bold <?= $cores[$item['categoria']] ?>">
                                <?= $item['categoria'] ?>
                            </div>
                            <img src="imagens/<?= $item['imagem'] ?>" class="card-img-top">

                            <div class="card-body">
                                <h4 class="card-title"><?= htmlspecialchars($item['nome']) ?></h4>

                            </div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item">
                                    <h5 class="card-text">R$
                                        <?= htmlspecialchars(number_format($item['valor'], 2, ",", ".")) ?></h5>
                                </li>
                                <li class="list-group-item"><?= spanEstoque($item) ?></li>
                            </ul>
                            <div class="card-footer text-center">
                                <a href="detalhes.php?id=<?= $item['id'] ?>" class="btn btn-primary">Ver detalhes</a>
                            </div>

                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>
        <?php require "footer.php"; ?>
    </body>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>