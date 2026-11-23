<?php require __DIR__ . '/cabecalho.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Agendar: <?= htmlspecialchars($servico['nome']) ?></h5>
            </div>
            <div class="card-body">
                <?php require __DIR__ . '/mensagens.php'; ?>

                <p>Valor: <strong>R$ <?= htmlspecialchars($servico['valor_formatado']) ?></strong></p>

                <form action="index.php?acao=adicionar" method="POST">
                    <input type="hidden" name="servico_id" value="<?= (int) $servico['id'] ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do animal</label>
                        <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required
                               value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="especie" class="form-label">Espécie</label>
                        <input type="text" class="form-control" id="especie" name="especie" maxlength="30" required
                               placeholder="Cachorro, gato..."
                               value="<?= htmlspecialchars($dados['especie'] ?? '') ?>">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sexo" class="form-label">Sexo</label>
                            <select class="form-select" id="sexo" name="sexo" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($sexos as $valor => $rotulo): ?>
                                    <option value="<?= htmlspecialchars($valor) ?>" <?= ($dados['sexo'] ?? '') === $valor ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($rotulo) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="idade" class="form-label">Idade (anos)</label>
                            <input type="number" class="form-control" id="idade" name="idade" min="0" max="100" required
                                   value="<?= htmlspecialchars($dados['idade'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="data" class="form-label">Data</label>
                            <input type="date" class="form-control" id="data" name="data" required
                                   min="<?= htmlspecialchars($hoje) ?>"
                                   value="<?= htmlspecialchars($dados['data'] ?? '') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="horario" class="form-label">Horário</label>
                            <select class="form-select" id="horario" name="horario" required>
                                <option value="">Selecione...</option>
                                <?php foreach ($horarios as $horario): ?>
                                    <option value="<?= htmlspecialchars($horario) ?>" <?= ($dados['horario'] ?? '') === $horario ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($horario) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">Adicionar à solicitação</button>
                    <a href="index.php?acao=detalhes&id=<?= (int) $servico['id'] ?>" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
