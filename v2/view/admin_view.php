<?php require __DIR__ . '/cabecalho.php'; ?>

<h2>Painel do administrador</h2>

<?php require __DIR__ . '/mensagens.php'; ?>

<p>
    <a href="index.php?acao=novoServico" class="btn btn-success">Novo Serviço</a>
    <a href="index.php?acao=solicitacoes" class="btn btn-outline-success">Ver Solicitações</a>
</p>

<div class="card">
    <div class="card-body">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Categoria</th>
                    <th>Preço</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
            <?php if (empty($servicos)): ?>
                <tr><td colspan="6" class="text-center text-muted">Nenhum serviço cadastrado.</td></tr>
            <?php endif; ?>

            <?php foreach ($servicos as $servico): ?>
                <tr>
                    <td><?= (int) $servico['id'] ?></td>
                    <td><img src="imagens/<?= htmlspecialchars($servico['imagem']) ?>" alt="" width="50"></td>
                    <td><?= htmlspecialchars($servico['nome']) ?></td>
                    <td><?= htmlspecialchars($servico['categoria']) ?></td>
                    <td>R$ <?= htmlspecialchars($servico['valor_formatado']) ?></td>
                    <td>
                        <a href="index.php?acao=editarServico&id=<?= (int) $servico['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
                        <form action="index.php?acao=excluirServico" method="POST" class="d-inline">
                            <input type="hidden" name="id" value="<?= (int) $servico['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
