<?php require __DIR__ . '/cabecalho.php'; ?>

<h2>Solicitações dos clientes</h2>

<?php require __DIR__ . '/mensagens.php'; ?>

<p><a href="index.php?acao=admin" class="btn btn-outline-success">Voltar aos Serviços</a></p>

<?php if (empty($solicitacoes)): ?>
    <p class="text-muted">Nenhuma solicitação.</p>
<?php endif; ?>

<?php foreach ($solicitacoes as $solicitacao): ?>
    <div class="card mb-4">
        <div class="card-header">
            <strong>Solicitação #<?= (int) $solicitacao['id'] ?></strong>
            <span class="badge <?= htmlspecialchars($solicitacao['cor']) ?>"><?= htmlspecialchars($solicitacao['status']) ?></span>
            <small class="text-muted ms-2">
                <?= htmlspecialchars($solicitacao['cliente']) ?> | <?= htmlspecialchars($solicitacao['telefone']) ?> | feita em <?= htmlspecialchars($solicitacao['data']) ?>
            </small>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Sexo</th>
                        <th>Idade</th>
                        <th>Serviço</th>
                        <th>Data/Hora</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Cancelado em</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitacao['itens'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['animal']) ?> (<?= htmlspecialchars($item['especie']) ?>)</td>
                        <td><?= htmlspecialchars($item['sexo']) ?></td>
                        <td><?= (int) $item['idade'] ?></td>
                        <td><?= htmlspecialchars($item['servico']) ?></td>
                        <td><?= htmlspecialchars($item['data']) ?> <?= htmlspecialchars($item['horario']) ?></td>
                        <td>R$ <?= htmlspecialchars($item['valor']) ?></td>
                        <td><span class="badge <?= htmlspecialchars($item['cor']) ?>"><?= htmlspecialchars($item['status']) ?></span></td>
                        <td><?= htmlspecialchars($item['cancelado']) ?></td>
                        <td>
                            <?php if ($item['status'] === 'pendente'): ?>
                                <form action="index.php?acao=confirmar" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-success">Confirmar Atendimento</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end fw-bold">Total: R$ <?= htmlspecialchars($solicitacao['total']) ?></div>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/rodape.php'; ?>
