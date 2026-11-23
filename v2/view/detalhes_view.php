<?php require __DIR__ . '/cabecalho.php'; ?>

<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card border-primary">
            <div class="card-header">
                <h2 class="card-title p-2"><?= htmlspecialchars($servico['nome']) ?></h2>
                <span class="badge <?= htmlspecialchars($servico['cor']) ?>"><?= htmlspecialchars($servico['categoria']) ?></span>
            </div>
            <img src="imagens/<?= htmlspecialchars($servico['imagem']) ?>" class="card-img-top img-fluid" alt="">
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h3 class="text-success fw-bold">R$ <?= htmlspecialchars($servico['valor_formatado']) ?></h3>
                <p class="fs-5"><?= htmlspecialchars($servico['descricao']) ?></p>

                <?php if ($usuarioPerfil === 'cliente'): ?>
                    <a href="index.php?acao=solicitar&id=<?= (int) $servico['id'] ?>" class="btn btn-success">Agendar este serviço</a>
                <?php endif; ?>

                <?php if ($usuarioNome === ''): ?>
                    <a href="index.php?acao=login" class="btn btn-success">Entrar para agendar</a>
                <?php endif; ?>

                <a href="index.php?acao=catalogo" class="btn btn-secondary">Voltar</a>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
