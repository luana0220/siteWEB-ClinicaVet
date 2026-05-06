<?php

require 'dados.php';
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $idAnimal = $_POST['id'] ?? '';
    $nomeDono = $_POST['nome'] ?? '';
    $telefone = $_POST['telefone'];
    $numeroAnimais = $_POST['quantidade'];
    $animais = $_POST['animais'] ?? [];
    $dataGeracaoConfirm = getdate();

    $item = buscarItem($idAnimal, $itens);
    $erros = [];

    if (empty(trim($nomeDono))) {
        $erros[] = "O nome do dono do animal é obrifatório";
    }

    $telefoneVerificado = preg_replace('/\D/', '', $telefone);
    if (!strlen($telefoneVerificado) === 10 || !strlen($telefoneVerificado) === 11) {
        $erros[] = "Digite um telefone válido!";
    }

    if ($numeroAnimais === '' || !is_numeric($numeroAnimais)) {
        $erros[] = "A quantidade de animais é obrigatória e deve ser um número";
    } else if ($numeroAnimais < 1) {
        $erros[] = "É necessário informar pelo menos um animal";
    }

    if ($item == null) {
        $erros[] = "Não foi possível encontrar o item";
    }

    foreach ($animais as $indiceAnimal => $animal) {
        if(empty(trim($animal['nome']))) {
            $erros[] = "O nome do animal " . ($indiceAnimal + 1) . " não foi informado.O nome do animal é obrigatório";
        }

        if(empty(trim($animal['especie'])) || $animal['especie'] === "") {
             $erros[] = "A espécie do animal " . ($indiceAnimal + 1) . " não foi informada.A espécie do animal é obrigatório";
        }

        if(empty(trim($animal['sexo']))) {
            $erros[] = "O sexo do animal " . ($indiceAnimal + 1) . " (" . $animal['nome'] . ") não foi informado. O sexo é obrigatório";
        }

        if($animal['idade'] === '' || !is_numeric($animal['idade'])) {
            $erros[] = 'A idade é obrigatória e deve ser um número. O animal " . ($indiceAnimal + 1) . "  não teve sua idade informada';
        } else if($animal['idade'] < 0 || $animal['idade'] > 30) {
            $erros[] = 'A idade do animal " . ($indiceAnimal + 1) . "(" . deve estar entre 0 e 30.';
        }

        if(empty(trim($animal['horario']))) {
            $erros[] = "O horário do/da" . $item['nome'] . " do animal " . ($indiceAnimal + 1) . " não foi informada.O horário é obrigatório";
        }
    }

    $valorTotal = $numeroAnimais * $item['valor'];
} else {
    echo "<h3>Método inválido. Insira os dados pelo <a href='detalhes.php'>formulário</a>.</h3>";
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Confirmação da solicitação</title>
</head>

<body>
    <div class="container mt-5" style="max-width: 800px;">
        <?php if (!empty($erros)): ?>
            <div class="alert alert-danger">
                <strong>Corrija os erros abaixo:</strong>
                <ul class="mb-0 mt-2">
                    <?php foreach ($erros as $erro): ?>
                        <li><?= htmlspecialchars($erro) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <a href="index.php" class="btn btn-secondary">Voltar ao formulário</a>
            <?php exit(); ?>
        <?php endif; ?>
        <div class="card shadow-sm p-4">
            <div class="card-header text-<?= $cores[$item['categoria']] ?> text-center">
                <h4>Confirmação do agendamento</h4>
            </div>
            <div class="card-body">
                <div class="card-title mt-3">
                    <h5>Olá, <?= htmlspecialchars($nomeDono) ?></h5>
                    <p class="fs-6"><strong>Telefone:</strong> <?= htmlspecialchars($telefone) ?></p>
                </div>
                <div class="d-flex justify-content-between">
                    <span>Resumo do agendamento</span>
                    <span>Gerado em
                        <strong><?= $dataGeracaoConfirm['mday'] . "/" . $dataGeracaoConfirm['mon'] . "/" . $dataGeracaoConfirm['year'] . " " . $dataGeracaoConfirm['hours'] . ":" . $dataGeracaoConfirm['minutes'] ?></strong></span>
                </div>

                <div class="card mb-3">
                    <div
                        class="card-body d-flex justify-content-between align-items-start border border-light-subtle p-2">

                        <!-- alinha esquerda -->
                        <div class="text-start">
                            <p class="mb-1">
                                <strong>Serviço:</strong> <?= htmlspecialchars($item['nome']) ?>
                            </p>
                            <p class="fw-bold mb-1">Valor unitário: </p>
                            <p class="mb-0 fw-bold">Número de animais: </p>
                        </div>

                        <!-- alinha direita -->
                        <div class="text-end">
                            <p class="fw-bold mb-1">
                                ID #<?= htmlspecialchars($idAnimal) ?>
                            </p>
                            <p class="text-primary fw-bold mb-1">R$
                                <?= htmlspecialchars(number_format($item['valor'], 2, ",", ".")) ?>
                            </p>
                            <p class="mb-0 fw-bold">x <?= htmlspecialchars($numeroAnimais) ?> </p>
                        </div>

                    </div>
                </div>


                <?php foreach ($animais as $animal): ?>
                    <div class="card mt-2 border border-secondary ">
                        <div class="card-header text-<?= $cores[$item['categoria']] ?> fw-bold"> Nome:
                            <?= htmlspecialchars($animal['nome'] ?? 'Não informado') ?>
                        </div>

                        <div class="card-body d-flex justify-content-between align-items-start">
                            <div class="text-start">
                                <p class="mb-1 fs-6">
                                    <strong>Espécie:</strong>
                                </p>
                                <p class="fw-bold mb-1 fs-6">Sexo: </p>
                                <p class="mb-0 fw-bold fs-6">Idade: </p>
                            </div>

                            <!-- alinha direita -->
                            <div class="text-end">
                                <span class="badge fw-bold d-block bg-<?= $coresEspecies[$animal['especie']] ?> fs-6 mb-2">
                                    <?= htmlspecialchars(ucfirst($animal['especie'])) ?>
                                </span>
                                <span class="badge d-block fw-bold mb-1 bg-<?= $coresSexo[$animal['sexo']] ?> fs-6">
                                    <?= htmlspecialchars(ucfirst($animal['sexo'])) ?>
                                </span>
                                <span class="d-block-inline"></span>
                                <p class="mb-0 fw-bold d-block fs-6"><?= $animal['idade'] > 1 ? htmlspecialchars($animal['idade']) . "ano(s)" : "Nenhum ano completo" ?></p>
                            </div>

                        </div>

                        <div class="text-center border-top border-secondary p-1">
                            <h5 class="fw-bold text-success">Horário <?= $item['nome'] ?>: <?= $animal['horario'] ?>h</h5>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
            <div class="card-footer text-center">
                <h2 class="text-primary fw-bold">Valor total: <?= htmlspecialchars(number_format($valorTotal, "2", ",", ".")) ?></h2>
                <hr>
                <a href="index.php" class="btn btn-primary">Voltar ao início</a>
            </div>
        </div>
        <div class="card-footer">
            
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>