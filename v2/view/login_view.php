<?php require __DIR__ . '/cabecalho.php'; ?>

<div class="row">
    <div class="col-md-5 mx-auto">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">Login</h5>
            </div>
            <div class="card-body">
                <?php require __DIR__ . '/mensagens.php'; ?>

                <form action="index.php?acao=autenticar" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required
                               value="<?= htmlspecialchars($email) ?>">
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label">Senha</label>
                        <input type="password" class="form-control" id="senha" name="senha" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="lembrar" name="lembrar" <?= $email !== '' ? 'checked' : '' ?>>
                        <label for="lembrar" class="form-check-label">Lembrar meu e-mail</label>
                    </div>
                    <button type="submit" class="btn btn-success w-100">Entrar</button>
                </form>

                <p class="mt-3 mb-0">Não tem conta? <a href="index.php?acao=cadastro">Criar conta</a></p>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/rodape.php'; ?>
