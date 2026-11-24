<?php
require __DIR__.'/includes/auth.php';
if (usuario_atual()) { header('Location: dashboard.php'); exit; }
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mb_strtolower(trim($_POST['email'] ?? ''));
    try {
        $st = db()->prepare('SELECT id,senha_hash FROM usuarios WHERE email=?');
        $st->execute([$email]);
        $row = $st->fetch();
        // password_verify também roda com hash falso para não revelar se o e-mail existe pelo tempo de resposta
        $ok = password_verify($_POST['senha'] ?? '', $row['senha_hash'] ?? '$2y$10$abcdefghijklmnopqrstuuABCDEFGHIJKLMNOPQRSTUVWXYZ01234');
        if (csrf_ok() && $row && $ok) { entrar((int)$row['id'], !empty($_POST['lembrar'])); header('Location: dashboard.php'); exit; }
        $erro = 'E-mail ou senha incorretos.';
    } catch (Throwable $t) { $erro = 'Serviço indisponível no momento. Tente novamente.'; }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecoleta - Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
</head>

<body>
    <header id="p-lateral">
        <div class="res-lateral">
            <h1>Reciclar rende ponto.<br>Ponto vira experiência.</h1>
            <p>Entregue papel, plástico, vidro, metal, eletrônico ou orgânico em um ponto de coleta parceiro e converta
                em pontos.
                Troque por ingressos para cinema, shows, descontos em bem-estar e muito mais.</p>
            <div class="texto-painel-lateral">
                <div class="texto-painel">
                    <div class="n">12.4t</div>
                    <div class="l">Material coletado</div>
                </div>
                <div class="texto-painel">
                    <div class="n">3.8k</div>
                    <div class="l">Usuários ativos</div>
                </div>
                <div class="texto-painel">
                    <div class="n">210</div>
                    <div class="l">Benefícios parceiros</div>
                </div>
            </div>
        </div>
        <div></div>
        </div>
    </header>
    <main id="vip">
        <section id="login-card">
            <h2 class="login-t">Entrar na sua conta</h2>
            <form method="post" action="login.php">
                <?php if ($erro): ?><p role="alert" style="color:#B23B2E"><?= e($erro) ?></p><?php endif; ?>
                <?php if (isset($_GET["sucesso"])): ?><p role="status" style="color:#2d6a4f">Cadastro realizado! Faça login para continuar.</p><?php endif; ?>
                <?= csrf_campo() ?>
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="Digite seu e-mail" autocomplete="username" required>
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" autocomplete="current-password" required>
                <div><input class="chk-box" type="checkbox" id="lembrar" name="lembrar" value="1"><label for="lembrar" class="tmc">Manter conectado?</label></div>
                <input class="login-button" type="submit" value="Entrar">
                <p>Ainda não é cadastrado? <a href="cadastro.php">Clique aqui</a></p>
            </form>
        </section>
    </main>
    <script src="js/login.js">
    </script>
</body>

</html>