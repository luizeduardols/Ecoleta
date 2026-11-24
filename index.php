<?php
require __DIR__.'/includes/auth.php';
$logado = usuario_atual();
$materiais = $beneficios = $parceiros = [];
$stats = ['usuarios'=>0,'kg'=>0,'parceiros'=>0];
try { // a página funciona mesmo sem banco configurado
    $pdo = db();
    $materiais = $pdo->query("SELECT nome,icone,pontos_por_kg FROM materiais WHERE ativo=1 ORDER BY id")->fetchAll();
    $beneficios = $pdo->query("SELECT b.titulo,b.custo_pontos,p.nome parceiro FROM beneficios b LEFT JOIN parceiros p ON p.id=b.parceiro_id WHERE b.ativo=1 ORDER BY b.custo_pontos LIMIT 6")->fetchAll();
    $parceiros = $pdo->query("SELECT nome,categoria,cidade FROM parceiros WHERE ativo=1 AND (categoria IS NULL OR categoria<>'Ponto de coleta') ORDER BY nome LIMIT 8")->fetchAll();
    $stats['usuarios'] = (int)$pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    $stats['kg'] = (float)$pdo->query("SELECT COALESCE(SUM(peso_kg),0) FROM entregas")->fetchColumn();
    $stats['parceiros'] = (int)$pdo->query("SELECT COUNT(*) FROM parceiros WHERE ativo=1")->fetchColumn();
} catch (Throwable $t) {}
if (!$materiais) $materiais = [['nome'=>'Papel','icone'=>'📄','pontos_por_kg'=>20],['nome'=>'Plástico','icone'=>'🧴','pontos_por_kg'=>30],['nome'=>'Vidro','icone'=>'🍾','pontos_por_kg'=>40],['nome'=>'Metal','icone'=>'🥫','pontos_por_kg'=>50],['nome'=>'Eletrônico','icone'=>'🔋','pontos_por_kg'=>100],['nome'=>'Orgânico','icone'=>'🍌','pontos_por_kg'=>10]];
if (!$beneficios) $beneficios = [['titulo'=>'2 ingressos de cinema','custo_pontos'=>800,'parceiro'=>'Cine Boa Viagem'],['titulo'=>'Sessão de massagem','custo_pontos'=>1200,'parceiro'=>'Espaço Zen Olinda'],['titulo'=>'Ingresso para show local','custo_pontos'=>1500,'parceiro'=>'Casa de Shows Frevo']];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ecoleta — Reciclar rende ponto</title>
  <meta name="description" content="Entregue recicláveis em pontos de coleta parceiros, acumule pontos e troque por cinema, shows e bem-estar.">
  <link rel="stylesheet" href="css/landing.css">
</head>
<body>
<header class="topo">
  <a class="marca" href="index.php"><span>♻️</span>Ecoleta</a>
  <nav>
    <a href="#como-funciona">Como funciona</a>
    <a href="#materiais">Materiais</a>
    <a href="#beneficios">Benefícios</a>
    <?php if ($logado): ?>
      <a class="btn" href="inicio.php">Minha conta</a>
    <?php else: ?>
      <a href="login.php">Entrar</a><a class="btn" href="cadastro.php">Criar conta</a>
    <?php endif; ?>
  </nav>
</header>

<main>
  <section class="hero">
    <div class="hero-texto">
      <h1>Reciclar rende ponto. Ponto vira experiência.</h1>
      <p>Leve papel, plástico, vidro, metal e eletrônicos a um ponto de coleta parceiro, acumule pontos e troque por ingressos de cinema, shows e descontos em bem-estar.</p>
      <div class="acoes">
        <a class="btn grande" href="<?= $logado ? 'inicio.php' : 'cadastro.php' ?>">Começar a reciclar</a>
        <a class="link" href="#como-funciona">Ver como funciona</a>
      </div>
    </div>
    <aside class="ticket" aria-label="Exemplo de recibo de entrega">
      <div class="ticket-topo"><span>Entrega no Ecoponto</span><small>Recife · PE</small></div>
      <ul>
        <li><span>🧴 Plástico · 3,2 kg</span><b>+96</b></li>
        <li><span>🍾 Vidro · 1,5 kg</span><b>+60</b></li>
        <li><span>🥫 Metal · 0,8 kg</span><b>+40</b></li>
      </ul>
      <div class="ticket-total"><span>Pontos desta entrega</span><b>196 pts</b></div>
      <div class="ticket-meta">
        <span>Próximo resgate: 2 ingressos de cinema</span>
        <div class="barra"><i style="width:62%"></i></div>
        <small>496 / 800 pts</small>
      </div>
    </aside>
  </section>

  <section class="faixa-stats">
    <div><b><?= $stats['usuarios'] ? number_format($stats['usuarios'],0,',','.') : '3,8 mil' ?></b><span>pessoas reciclando</span></div>
    <div><b><?= $stats['kg'] ? number_format($stats['kg'],1,',','.').' kg' : '12,4 t' ?></b><span>de material coletado</span></div>
    <div><b><?= $stats['parceiros'] ?: 210 ?></b><span>parceiros e benefícios</span></div>
  </section>

  <section id="como-funciona" class="bloco">
    <h2>Como funciona</h2>
    <ol class="passos">
      <li><h3>Separe</h3><p>Lave e separe os recicláveis em casa. Quanto mais limpo, mais pontos.</p></li>
      <li><h3>Entregue</h3><p>Leve até um ponto de coleta parceiro. O peso vira pontos na hora, direto na sua conta.</p></li>
      <li><h3>Troque</h3><p>Escolha um benefício no catálogo e resgate com o código gerado pelo app.</p></li>
    </ol>
  </section>

  <section id="materiais" class="bloco claro">
    <h2>O que você pode reciclar</h2>
    <p class="sub">Cada material rende uma quantidade de pontos por quilo.</p>
    <div class="grade-materiais">
      <?php foreach ($materiais as $m): ?>
        <div class="material"><span class="emoji"><?= e($m['icone'] ?: '♻️') ?></span><h3><?= e($m['nome']) ?></h3><p><?= (int)$m['pontos_por_kg'] ?> pts/kg</p></div>
      <?php endforeach; ?>
    </div>
  </section>

  <section id="beneficios" class="bloco">
    <h2>Troque seus pontos por experiências</h2>
    <div class="grade-beneficios">
      <?php foreach ($beneficios as $b): ?>
        <article class="beneficio"><small><?= e($b['parceiro'] ?? 'Parceiro') ?></small><h3><?= e($b['titulo']) ?></h3><b><?= number_format((int)$b['custo_pontos'],0,',','.') ?> pts</b></article>
      <?php endforeach; ?>
    </div>
    <?php if ($parceiros): ?>
      <p class="parceiros"><span>Quem já faz parte:</span>
        <?php foreach ($parceiros as $p): ?><em><?= e($p['nome']) ?></em><?php endforeach; ?></p>
    <?php endif; ?>
  </section>

  <section class="chamada">
    <h2>Seu lixo de hoje pode ser a sessão de cinema de amanhã.</h2>
    <a class="btn grande amarelo" href="<?= $logado ? 'inicio.php' : 'cadastro.php' ?>">Criar minha conta grátis</a>
  </section>
</main>

<footer class="rodape">
  <span>© <?= date('Y') ?> Ecoleta</span>
  <span>ecoleta@ppr.com.br · (81) 90000-0000</span>
  <nav><a href="sobre.php">Sobre</a><a href="contato.php">Contato</a><a href="login.php">Entrar</a></nav>
</footer>
</body>
</html>
