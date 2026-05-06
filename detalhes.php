<?php
require 'dados.php';



$idItem = $_GET['id'] ?? "";
$infosItem = buscarItem($idItem, $itens) ?? null;
$numHorariosDisponiveis = count($infosItem['horarios']);

if ($numHorariosDisponiveis == 0) {
    $corSpanHorarios = "bg-danger";
} else if ($numHorariosDisponiveis <= 3) {
    $corSpanHorarios = "bg-warning";
} else {
    $corSpanHorarios = "bg-success";
}

if ($infosItem == null) {
    echo "<p>Este Item não existe. Volte para a página:</p>";
    echo "<a href=\"index.php\">Voltar</a>";
}

$numeroAnimais = $_POST['quantidade'] ?? '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $numeroAnimais < 1) {
    echo "<div class=\"alert alert-danger\" role=\"alert\">Você precisa agendar pelo menos 1 animal</div>";
}


$nomeDono = $_POST['nome'] ?? "";
$telefoneDono = $_POST['telefone'] ?? "";
$horariosDoItemDisponiveis = array_intersect($horariosDisponiveis, $infosItem['horarios']);


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Detalhes: <?= $infosItem['nome'] ?></title>
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-sm-6 mb-3 mb-sm-0">
                <div class="card border-primary">
                    <div class="card-header card border-primary mb-0">
                        <h2 class="card-title p-2"><?= $infosItem['nome'] ?></h2>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item border-0">
                                <span
                                    class="badge text-<?= $cores[$infosItem['categoria']] ?>"><?= $infosItem['categoria'] ?></span>
                            </li>
                            <li class="list-group-item">
                                <span class="badge text-<?= $corSpanHorarios ?>"><?= $numHorariosDisponiveis ?> horários
                                    disponíves</span>
                            </li>
                        </ul>
                    </div>
                    <img src="imagens/<?= $infosItem['imagem'] ?>" class="card-img-top img-fluid"
                        style="max-height: 350px; object-fit: contain;">
                    <div class="card-body">
                        <h3 class="card-text text-success fw-bold">R$
                            <?= htmlspecialchars(number_format($infosItem['valor'], 2, ",", ".")) ?></h3>
                        <h5 class="card-text"><?= htmlspecialchars($infosItem['descricao']) ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">

                <div class="card-header card border-primary mb-0 text-<?= $cores[$infosItem['categoria']] ?>">
                    <h3 class="card-title p-2">Solicitar <?= htmlspecialchars($infosItem['nome']) ?></h3>
                </div>
                <div class="card-body">
                    <?php if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($numeroAnimais)): ?>
                        <form method="POST">
                            <fieldset class="border p-3 rounded mb-3">
                                <legend class="w-auto px-2 fs-5">Dados do dono</legend>
                                <div class="mb-3">
                                    <label for="nome" class="form-label">Nome completo: </label>
                                    <input id="nome" type="text" name="nome" placeholder="Ex: Luana de Melo Duarte"
                                        class="form-control" maxlength="80" required>
                                </div>

                                <div class="mb-3">
                                    <label for="telefone" class="form-label">Telefone para contato: </label>
                                    <input id="telefone" type="tel" name="telefone" placeholder="Ex: 31 972633124"
                                        class="form-control" required>
                                </div>
                            </fieldset>

                            <fieldset class="border p-3 rounded mb-3">
                                <legend class="w-auto px-2 fs-5">Quantidade de animais</legend>

                                <div class="mb-3">
                                    <label for="quantidade" class="form-label">Quantidade de animais para agendar o
                                        serviço</label>
                                    <input type="number" name="quantidade" id="quantidade" class="form-control" required>
                                </div>
                            </fieldset>
                            <div class="card-footer text-center">

                                <div class="form-check d-flex justify-content-center align-items-center gap-2">
                                </div>
                                <button type="submit" class="btn btn-secondary">Continuar ➡️</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <form action="resultado.php" method="POST">
                            <div class="accordion" id="accordionForm">

                                <?php for ($i = 0; $i < $numeroAnimais; $i++): ?>

                                    <div class="accordion-item">

                                        <h2 class="accordion-header">
                                            <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#animal<?= $i ?>"
                                                aria-expanded="<?= $i == 0 ? 'true' : 'false' ?>"
                                                aria-controls="animal<?= $i ?>">
                                                Animal <?= $i + 1 ?>
                                            </button>
                                        </h2>

                                        <div id="animal<?= $i ?>" class="accordion-collapse collapse show"
                                            data-bs-parent="#accordionForm">
                                            <div class="accordion-body">
                                                <div class="card shadow-sm p-4 mb-3">
                                                    <h4>Animal <?= $i + 1 ?></h4>

                                                    <fieldset class="border p-3 rounded">

                                                        <div class="mb-3">
                                                            <label class="form-label">Nome</label>
                                                            <input type="text" name="animais[<?= $i ?>][nome]"
                                                                class="form-control" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Espécie</label>
                                                            <select name="animais[<?= $i ?>][especie]" class="form-select">
                                                                <option value="">Selecione</option>
                                                                <option value="Cachorro">Cachorro</option>
                                                                <option value="Gato">Gato</option>
                                                                <option value="Coelho">Coelho</option>
                                                            </select>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Sexo</label><br>
                                                            <div class="form-check">
                                                                <input type="radio" name="animais[<?= $i ?>][sexo]"
                                                                    value="Macho" id="sexoM" required>
                                                                <label class="form-check-label" for="sexoM">Macho</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input type="radio" name="animais[<?= $i ?>][sexo]"
                                                                    value="Femea" id="sexoF" required>
                                                                <label class="form-check-label" for="sexoF">Fêmea</label>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Idade</label>
                                                            <input type="number" name="animais[<?= $i ?>][idade]"
                                                                class="form-control" min="0" max="100" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label">Horário</label>

                                                            <?php foreach ($horariosDisponiveis as $horario): ?>
                                                                <?php $disponivel = in_array($horario, $infosItem['horarios']); ?>

                                                                <div
                                                                    class="form-check <?= $disponivel ? 'text-success fw-bold' : '' ?>">
                                                                    <input class="form-check-input" type="radio"
                                                                        name="animais[<?= $i ?>][horario]" value="<?= $horario ?>"
                                                                        <?= $disponivel ? '' : 'disabled' ?> required>

                                                                    <label class="form-check-label">
                                                                        <?= $horario ?>
                                                                    </label>
                                                                </div>
                                                            <?php endforeach; ?>

                                                        </div>

                                                    </fieldset>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                <?php endfor; ?>

                            </div>



                            <input type="hidden" name="nome" value="<?= $nomeDono ?>">
                            <input type="hidden" name="telefone" value="<?= $telefoneDono ?>">
                            <input type="hidden" name="quantidade" value="<?= $numeroAnimais ?>">
                            <input type="hidden" name="id" value="<?= $infosItem['id'] ?>">

                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-secondary mt-3">
                                    Continuar ➡️
                                </button>
                            </div>


                        </form>

                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>
</body>

</html>