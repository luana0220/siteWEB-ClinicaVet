<?php require __DIR__ . '/cabecalho.php'; ?>

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Cadastro de usuário</h5>
            </div>
            <div class="card-body">
                <?php require __DIR__ . '/mensagens.php'; ?>

                <form action="index.php?acao=cadastrar" method="POST">
                    <div class="mb-3">
                        <label for="nome" class="form-label">Nome completo</label>
                        <input type="text" class="form-control" id="nome" name="nome" maxlength="100" required
                               value="<?= htmlspecialchars($dados['nome'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" maxlength="150" required
                               value="<?= htmlspecialchars($dados['email'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="telefone" class="form-label">Telefone</label>
                        <input type="tel" class="form-control" id="telefone" name="telefone" maxlength="20"
                               placeholder="(31) 99999-9999" required
                               value="<?= htmlspecialchars($dados['telefone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha"
                               placeholder="Mínimo de 8 caracteres" minlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirma_senha" class="form-label">Confirmação de senha</label>
                        <input type="password" class="form-control" id="confirma_senha" name="confirma_senha"
                               minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Cadastrar</button>
                </form>

                <p class="mt-3 mb-0"><a href="index.php?acao=login">Já possuo conta</a></p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
