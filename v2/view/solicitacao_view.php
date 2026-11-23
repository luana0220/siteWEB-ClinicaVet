<?php require __DIR__ . '/cabecalho.php'; ?>

<h2>Solicitação atual</h2>
<p>Cada atendimento tem seu animal, serviço, data e horário. Adicione quantos precisar antes de enviar.</p>

<?php require __DIR__ . '/mensagens.php'; ?>

<p><a href="index.php?acao=catalogo" class="btn btn-success">Adicionar Atendimento</a></p>

<div class="card mb-4">
    <div class="card-body">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Animal</th>
                    <th>Espécie</th>
                    <th>Sexo</th>
                    <th>Idade</th>
                    <th>Serviço</th>
                    <th>Data/Hora</th>
                    <th>Valor</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($atendimentos)): ?>
                <tr><td colspan="8" class="text-center text-muted">Nenhum atendimento adicionado.</td></tr>
            <?php endif; ?>

            <?php foreach ($atendimentos as $atendimento): ?>
                <tr>
                    <td><?= htmlspecialchars($atendimento['nome']) ?></td>
                    <td><?= htmlspecialchars($atendimento['especie']) ?></td>
                    <td><?= htmlspecialchars($atendimento['sexo']) ?></td>
                    <td><?= (int) $atendimento['idade'] ?></td>
                    <td><?= htmlspecialchars($atendimento['servico']) ?></td>
                    <td><?= htmlspecialchars($atendimento['data_formatada']) ?> <?= htmlspecialchars($atendimento['horario']) ?></td>
                    <td>R$ <?= htmlspecialchars($atendimento['valor_formatado']) ?></td>
                    <td>
                        <form action="index.php?acao=remover" method="POST" class="d-inline">
                            <input type="hidden" name="indice" value="<?= (int) $atendimento['indice'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Remover</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer text-end fw-bold">Total: R$ <?= htmlspecialchars($total) ?></div>
</div>

<?php if (!empty($atendimentos)): ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="index.php?acao=agendar" method="POST">
                        <div class="mb-3">
                            <label for="telefone" class="form-label">Telefone para contato</label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" maxlength="20"
                                   placeholder="(31) 99999-9999" required
                                   value="<?= htmlspecialchars($telefone) ?>">
                        </div>
                        <button type="submit" class="btn btn-success">Enviar solicitação</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php require __DIR__ . '/rodape.php'; ?>
