<?php require __DIR__ . '/cabecalho.php'; ?>
<?php require __DIR__ . '/mensagens.php'; ?>

<div class="card mb-4">
    <h2 class="card-title m-3">Serviços disponíveis da clínica</h2>
    <div class="card-body">
        <div class="list-group list-group-horizontal mb-2 gap-1">
            <a href="index.php?acao=catalogo"
               class="list-group-item list-group-item-action fw-bold text-center <?= $categoria === 0 ? 'active' : '' ?>">Todos</a>
            <?php foreach ($categorias as $item): ?>
                <a href="index.php?acao=catalogo&categoria=<?= (int) $item['id'] ?>"
                   class="list-group-item list-group-item-action fw-bold text-center <?= $categoria === (int) $item['id'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($item['nome']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row text-center">
    <?php if (empty($servicos)): ?>
        <p class="text-muted">Nenhum serviço cadastrado.</p>
    <?php endif; ?>

    <?php foreach ($servicos as $servico): ?>
        <div class="col-sm-6 col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header text-center text-white fw-bold <?= htmlspecialchars($servico['cor']) ?>">
                    <?= htmlspecialchars($servico['categoria']) ?>
                </div>
                <img src="imagens/<?= htmlspecialchars($servico['imagem']) ?>" class="card-img-top" alt="">
                <div class="card-body">
                    <h4 class="card-title"><?= htmlspecialchars($servico['nome']) ?></h4>
                    <h5 class="card-text">R$ <?= htmlspecialchars($servico['valor_formatado']) ?></h5>
                </div>
                <div class="card-footer text-center">
                    <a href="index.php?acao=detalhes&id=<?= (int) $servico['id'] ?>" class="btn btn-primary">Ver detalhes</a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
