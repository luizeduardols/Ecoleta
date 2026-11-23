<?php require __DIR__.'/includes/auth.php'; $u = exigir_login(); ?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Benefícios</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <section id="d-lateral">
        <span id="principal">Pontos por reciclagem</span>
        <nav>
            <a href="inicio.php">
                <img class="icone" src="img/inicio.svg">
                <span class="s-txt">Início</span>
            </a>
            <a href="dashboard.php">
                <img class="icone" src="img/dashboard.svg">
                <span class="s-txt">Dashboard</span>
            </a>
            <a href="beneficio.php" class="ativo">
                <img class="icone" src="img/ticket.svg">
                <span class="s-txt">Benefícios</span>
            </a>
            <a href="historico.php">
                <img class="icone" src="img/historico.svg">
                <span class="s-txt">Histórico</span>
            </a>
            <a href="perfil.php">
                <img class="icone" src="img/perfil.svg">
                <span class="s-txt">Perfil</span>
            </a>
            <a href="sobre.php">
                <img class="icone" src="img/sobre.svg">
                <span class="s-txt">Sobre</span>
            </a>
            <a href="contato.php">
                <img class="icone" src="img/contato.svg">
                <span class="s-txt">Contato</span>
            </a>
        </nav>
        <details class="menu-usuario">
            <summary>
                <span class="avatar-menu" aria-hidden="true"><?= e(iniciais($u["nome"])) ?></span>
                <span class="usuario-dados"><strong><?= e($u["nome"]) ?></strong><small><?= e($u["email"]) ?></small></span>
            </summary>
            <div class="submenu-usuario">
                <a href="perfil.php">Meu perfil</a>
                <?php if ($u["perfil"]==="admin"): ?><a href="admin/index.php">Administração</a><?php endif; ?>
        <a href="logout.php">Sair</a>
            </div>
        </details>
    </section>
    <main class="conteudo-pagina">
        <div class="conteiner-pagina">
            <h1>Benefícios</h1>
            <p class="texto-secundario grupo-formulario-ultimo">Escolha como gastar seus pontos.</p>
            <section class="cartao-pagina">
                <h2>Em breve</h2>
                <p class="texto-secundario">Novos benefícios estarão disponíveis para resgate nesta página.</p>
            </section>
        </div>
    </main>
</body>

</html>