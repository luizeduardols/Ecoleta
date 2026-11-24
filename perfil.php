<?php
require __DIR__.'/includes/auth.php';
$u = exigir_login();
$msg = $erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tel = trim($_POST['telefone'] ?? '');
    if (!csrf_ok()) $erro = 'Sessão expirada. Recarregue a página e tente de novo.';
    elseif (!telefone_valido($tel)) $erro = 'Informe um telefone válido com DDD.';
    else {
        $novo = formatar_telefone($tel);
        // WHERE id = ?: cada usuário altera só o próprio telefone
        db()->prepare('UPDATE usuarios SET telefone = ? WHERE id = ?')->execute([$novo, $u['id']]);
        $u['telefone'] = $novo;
        $msg = 'Dados atualizados!';
    }
}
$st = db()->prepare('SELECT COUNT(*) FROM entregas WHERE usuario_id = ?'); $st->execute([$u['id']]);
$itens = (int)$st->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Perfil - Ecoleta</title>
  <link rel="stylesheet" href="css/style.css">
</head>

<body>
  <section id="d-lateral">
    <span id="principal">Ecoleta</span>
    <nav>
      <a href="inicio.php"><img class="icone" src="img/inicio.svg" alt="" /><span class="s-txt">Início</span></a>
      <a href="dashboard.php"><img class="icone" src="img/dashboard.svg" /><span class="s-txt">Dashboard</span></a>
      <a href="beneficio.php"><img class="icone" src="img/ticket.svg" /><span class="s-txt">Benefícios</span></a>
      <a href="historico.php"><img class="icone" src="img/historico.svg" /><span class="s-txt">Histórico</span></a>
      <a href="perfil.php" class="ativo"><img class="icone" src="img/perfil.svg" /><span
          class="s-txt">Perfil</span></a>
      <a href="sobre.php"><img class="icone" src="img/sobre.svg" /><span class="s-txt">Sobre</span></a>
      <a href="contato.php"><img class="icone" src="img/contato.svg" alt="" /><span class="s-txt">Contato</span></a>
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

  <section class="perfil">
    <div class="perfil-topo">
      <div class="avatar"><?= e(iniciais($u["nome"])) ?></div>
      <div class="perfil-nome"><?= e($u["nome"]) ?></div>
      <div class="perfil-email"><?= e($u["email"]) ?></div>
    </div>

    <div class="perfil-corpo">
      <div class="info">
        <span class="info-1">Pontos acumulados</span>
        <span class="info-2"><?= number_format((int)$u['pontos'], 0, ',', '.') ?> pts</span>
      </div>
      <div class="info">
        <span class="info-1">Itens reciclados</span>
        <span class="info-2"><?= $itens ?></span>
      </div>
      <div class="info">
        <span class="info-1">Membro desde</span>
        <span class="info-2"><?= e(date('m/Y', strtotime($u['criado_em']))) ?></span>
      </div>

      <form method="post" action="perfil.php" id="form-perfil" class="perfil-form">
        <?= csrf_campo() ?>
        <?php if ($msg): ?><p class="perfil-ok" role="status"><?= e($msg) ?></p><?php endif; ?>
        <?php if ($erro): ?><p class="perfil-erro" role="alert"><?= e($erro) ?></p><?php endif; ?>
        <label for="p-nome">Nome</label>
        <input type="text" id="p-nome" value="<?= e($u['nome']) ?>" disabled>
        <label for="telefone">Telefone</label>
        <input type="text" id="telefone" name="telefone" data-mascara="telefone" maxlength="15" value="<?= e($u['telefone']) ?>" required>
        <button type="submit" class="botao">Salvar alterações</button>
      </form>
    </div>
  </section>
  <nav class="d-dropdown">
    <div>Minha conta</div>
    <ul class="d-menu">
      <li><a href="#"><img class="icone" src="">Perfil</a></li>
      <li><a href="#"><img class="icone" src="">Histórico</a></li>
      <li><a href="#"><img class="icone" src="">Meus pontos</a></li>
      <hr>
      <li><a href="logout.php" class="sair">Sair</a></li>
    </ul>
  </nav>

  <script src="js/mascaras.js"></script>
  <script src="js/perfil.js"></script>
</body>

</html>