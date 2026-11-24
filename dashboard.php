<?php require __DIR__.'/includes/auth.php'; $u = exigir_login(); ?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/style.css" />
</head>

<body id="dash">
  <section id="d-lateral">
    <span id="principal">Ecoleta</span>
    <nav>
      <a href="inicio.php"><img class="icone" src="img/inicio.svg" /><span class="s-txt">Início</span></a>
      <a href="dashboard.php" class="ativo"><img class="icone" src="img/dashboard.svg" /><span
          class="s-txt">Dashboard</span></a>
      <a href="beneficio.php"><img class="icone" src="img/ticket.svg" /><span class="s-txt">Benefícios</span></a>
      <a href="historico.php"><img class="icone" src="img/historico.svg" /><span class="s-txt">Histórico</span></a>
      <a href="perfil.php"><img class="icone" src="img/perfil.svg" /><span class="s-txt">Perfil</span></a>
      <a href="sobre.php"><img class="icone" src="img/sobre.svg" /><span class="s-txt">Sobre</span></a>
      <a href="contato.php"><img class="icone" src="img/contato.svg" /><span class="s-txt">Contato</span></a>
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
  <section id="tela-1">
    <div class="i-topo">
      <div><?= e(iniciais($u["nome"])) ?></div>
      <p>Bem-vindo(a), <?= e(strtok($u['nome'], ' ')) ?></p>
    </div>
    <div class="interno-a">
      <p>Saldo Disponível</p>
      <span><?= number_format((int)$u['pontos'], 0, ',', '.') ?> pts</span>
    </div>
    <div class="interno-a">
      <p>Materiais entregues no mês</p>
      <span>134 Kg</span>
    </div>
    <div class="interno-a">
      <p>Pontos resgatados no mês</p>
      <span>1500 pts</span>
    </div>
    <div class="interno-b conta-dados">
      <p>Seus dados</p>
      <dl class="dados">
        <dt>Nome</dt><dd><?= e($u['nome']) ?></dd>
        <dt>E-mail</dt><dd><?= e(mascarar_email($u['email'])) ?></dd>
        <dt>CPF</dt><dd><?= e(mascarar_cpf($u['cpf'])) ?></dd>
        <dt>Telefone</dt><dd><?= e($u['telefone'] ?: 'não informado') ?></dd>
      </dl>
      <a class="botao" href="perfil.php">Editar perfil</a>
    </div>
    <div class="interno-b">
      <p>Resumo do seu Histórico</p>
      <table>
        <tr>
          <th>Data da Entrega</th>
          <th>Material</th>
          <th>Peso</th>
          <th>Local</th>
          <th>Código</th>
          <th>Valor</th>
          <th>Pontos</th>
        </tr>
        <tr>
          <td>22/09/2026</td>
          <td>Ferro</td>
          <td>10kg</td>
          <td>Av. Padre Inglês</td>
          <td>0085</td>
          <td>R$5.00</td>
          <td>200</td>
        </tr>
        <tr>
          <td>21/09/2026</td>
          <td>Pet</td>
          <td>2kg</td>
          <td>Av. Norte</td>
          <td>0084</td>
          <td>R$2.00</td>
          <td>250</td>
        </tr>
        <tr>
          <td>20/09/2026</td>
          <td>Aluminio</td>
          <td>5kg</td>
          <td>Rua 48</td>
          <td>0083</td>
          <td>R$20.00</td>
          <td>800</td>
        </tr>
        <tr>
          <td>19/09/2026</td>
          <td>Papelão</td>
          <td>1kg</td>
          <td>Rua Sempre Viva</td>
          <td>0082</td>
          <td>R$2.50</td>
          <td>350</td>
        </tr>
        <tr>
          <td>18/09/2026</td>
          <td>Aluminio</td>
          <td>10kg</td>
          <td>Av. Rio Branco</td>
          <td>0081</td>
          <td>R$40.00</td>
          <td>1600</td>
        </tr>
        <tr>
          <td>17/09/2026</td>
          <td>Vidro</td>
          <td>5kg</td>
          <td>Av. Cde da Boa Vista</td>
          <td>0080</td>
          <td>R$4.00</td>
          <td>300</td>
        </tr>
        <tr>
          <td>16/09/2026</td>
          <td>Abs</td>
          <td>2kg</td>
          <td>Av. Dantas Barreto</td>
          <td>0079</td>
          <td>R$3.00</td>
          <td>300</td>
        </tr>
        <tr>
          <td>17/09/2026</td>
          <td>Pet</td>
          <td>5kg</td>
          <td>Av. Boa Viagem</td>
          <td>0078</td>
          <td>R$5.00</td>
          <td>400</td>
        </tr>
        <tr>
          <td>16/09/2026</td>
          <td>Vidro</td>
          <td>5kg</td>
          <td>Rua Padre Lemos</td>
          <td>0077</td>
          <td>R$5.00</td>
          <td>500</td>
        </tr>
        <tr>
          <td>15/09/2026</td>
          <td>Ferro</td>
          <td>5kg</td>
          <td>Av. Dom Bosco</td>
          <td>0076</td>
          <td>R$5.00</td>
          <td>500</td>
        </tr>
        <tr>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
          <th></th>
        </tr>
      </table>
    </div>
  </section>
  <footer></footer>
</body>

</html>