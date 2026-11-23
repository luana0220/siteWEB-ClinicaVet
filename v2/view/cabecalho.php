<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Animed</title>
    <link rel="stylesheet" href="css/bootstrap.min.css">
</head>

<body class="bg-light">

    <header class="bg-white shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand navbar-light py-3">
                <a class="navbar-brand d-flex align-items-center" href="index.php?acao=catalogo">
                    <img src="imagens/logo.png" alt="Clínica Animed" height="50" class="me-2">
                    <div>
                        <h4 class="mb-0 text-success fw-bold">Animed</h4>
                        <small>Cuidando do seu pet</small>
                    </div>
                </a>

                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?acao=catalogo">Serviços</a>
                    </li>
                    <?php if ($usuarioPerfil === 'cliente'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?acao=solicitacao">Solicitação Atual</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?acao=minhas">Minhas Solicitações</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($usuarioPerfil === 'administrador'): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?acao=admin">Painel do Administrador</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php?acao=solicitacoes">Solicitações</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($usuarioNome !== ''): ?>
                        <li class="nav-item ms-3">
                            <span class="navbar-text">Olá, <?= htmlspecialchars($usuarioNome) ?></span>
                        </li>
                        <li class="nav-item ms-3">
                            <form action="index.php?acao=sair" method="post" class="d-inline">
                                <button type="submit" class="btn btn-outline-secondary">
                                    Sair
                                </button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li class="nav-item ms-3">
                            <a href="index.php?acao=cadastro" class="btn btn-outline-success">Criar conta</a>
                        </li>
                        <li class="nav-item ms-2">
                            <a href="index.php?acao=login" class="btn btn-success">Entrar</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container mt-4">