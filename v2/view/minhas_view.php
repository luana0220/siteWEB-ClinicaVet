<?php require __DIR__ . '/cabecalho.php'; ?>

<h2>Minhas solicitações</h2>
<p>Olá, <?= htmlspecialchars($usuarioNome) ?>! Aqui estão somente as suas solicitações.</p>

<?php require __DIR__ . '/mensagens.php'; ?>

<p><a href="index.php?acao=catalogo" class="btn btn-success">Agendar Novo Serviço</a></p>

<?php if (empty($solicitacoes)): ?>
    <p class="text-muted">Nenhuma solicitação.</p>
<?php endif; ?>

<?php foreach ($solicitacoes as $solicitacao): ?>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <strong>Solicitação #<?= (int) $solicitacao['id'] ?></strong>
                <span class="badge <?= htmlspecialchars($solicitacao['cor']) ?>"><?= htmlspecialchars($solicitacao['status']) ?></span>
                <small class="text-muted ms-2">Feita em <?= htmlspecialchars($solicitacao['data']) ?></small>
            </div>
            <?php if ($solicitacao['status'] !== 'cancelada'): ?>
                <form action="index.php?acao=cancelar" method="POST">
                    <input type="hidden" name="id" value="<?= (int) $solicitacao['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger">Cancelar Solicitação Inteira</button>
                </form>
            <?php endif; ?>
        </div>
        <div class="card-body">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Animal</th>
                        <th>Serviço</th>
                        <th>Data/Hora</th>
                        <th>Valor</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($solicitacao['itens'] as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['animal']) ?> (<?= htmlspecialchars($item['especie']) ?>)</td>
                        <td><?= htmlspecialchars($item['servico']) ?></td>
                        <td><?= htmlspecialchars($item['data']) ?> <?= htmlspecialchars($item['horario']) ?></td>
                        <td>R$ <?= htmlspecialchars($item['valor']) ?></td>
                        <td>
                            <span class="badge <?= htmlspecialchars($item['cor']) ?>"><?= htmlspecialchars($item['status']) ?></span>
                            <?php if ($item['cancelado'] !== ''): ?>
                                <br><small class="text-muted">em <?= htmlspecialchars($item['cancelado']) ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($item['status'] !== 'cancelada'): ?>
                                <form action="index.php?acao=cancelarItem" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= (int) $item['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Cancelar Atendimento</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer text-end">
            Telefone: <?= htmlspecialchars($solicitacao['telefone']) ?> | <strong>Total: R$ <?= htmlspecialchars($solicitacao['total']) ?></strong>
        </div>
    </div>
<?php endforeach; ?>

<?php require __DIR__ . '/rodape.php'; ?>
