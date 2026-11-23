<?php require __DIR__ . '/cabecalho.php'; ?>

<div class="row">
    <div class="col-md-7 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><?= htmlspecialchars($titulo) ?></h5>
            </div>
            <div class="card-body">
                <?php require __DIR__ . '/mensagens.php'; ?>

                <form action="index.php?acao=salvarServico" method="POST">
                    <input type="hidden" name="id" value="<?= (int) $id ?>">

                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome do Serviço</label>
                        <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required
                               value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="id_categoria" class="form-label">Categoria</label>
                        <select class="form-select" id="id_categoria" name="id_categoria" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?= (int) $categoria['id'] ?>" <?= (int) ($dados['id_categoria'] ?? 0) === (int) $categoria['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categoria['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="valor" class="form-label">Preço (R$)</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="valor" name="valor" required
                               value="<?= htmlspecialchars($dados['valor'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="4" required><?= htmlspecialchars($dados['descricao'] ?? '') ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="imagem" class="form-label">Nome do arquivo de imagem</label>
                        <input type="text" class="form-control" id="imagem" name="imagem" maxlength="250"
                               placeholder="default.png"
                               value="<?= htmlspecialchars($dados['imagem'] ?? '') ?>">
                    </div>

                    <button type="submit" class="btn btn-success">Salvar Serviço</button>
                    <a href="index.php?acao=admin" class="btn btn-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
