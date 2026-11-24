<?php
require __DIR__.'/includes/auth.php';
if (usuario_atual()) { header('Location: dashboard.php'); exit; }

$erros = []; $v = ['nome'=>'','email'=>'','cpf'=>'','nascimento'=>'','telefone'=>'','cidade'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) $v[$k] = trim($_POST[$k] ?? '');
    $senha = $_POST['senha'] ?? ''; $conf = $_POST['confirma'] ?? '';

    if (!csrf_ok()) $erros['geral'] = 'Sessão expirada. Recarregue a página e tente de novo.';
    if (count(preg_split('/\s+/', $v['nome'], -1, PREG_SPLIT_NO_EMPTY)) < 2 || mb_strlen($v['nome']) > 120) $erros['nome'] = 'Informe nome e sobrenome.';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL) || strlen($v['email']) > 160) $erros['email'] = 'Digite um e-mail válido.';
    if ($v['nascimento'] !== '') {
        $d = DateTime::createFromFormat('Y-m-d', $v['nascimento']);
        $idade = $d ? $d->diff(new DateTime())->y : -1;
        if (!$d || $d > new DateTime() || $idade > 110) $erros['nascimento'] = 'Data de nascimento inválida.';
        elseif ($idade < 13) $erros['nascimento'] = 'É preciso ter pelo menos 13 anos.';
    }
    if (!cpf_valido($v['cpf'])) $erros['cpf'] = 'Informe um CPF válido.';
    if (!telefone_valido($v['telefone'])) $erros['telefone'] = 'Use DDD + número, ex.: (81) 99999-0000.';
    if (strlen($senha) < 8 || !preg_match('/[A-Za-z]/', $senha) || !preg_match('/\d/', $senha)) $erros['senha'] = 'Mínimo de 8 caracteres, com letras e números.';
    if ($senha !== $conf) $erros['confirma'] = 'As senhas não coincidem.';
    if (empty($_POST['termos'])) $erros['termos'] = 'Aceite os termos para continuar.';

    if (!$erros) {
        try {
            $st = db()->prepare('INSERT INTO usuarios (nome,email,cpf,senha_hash,data_nascimento,telefone,cidade) VALUES (?,?,?,?,?,?,?)');
            $st->execute([$v['nome'], mb_strtolower($v['email']), formatar_cpf($v['cpf']), password_hash($senha, PASSWORD_DEFAULT),
                $v['nascimento'] ?: null, formatar_telefone($v['telefone']), $v['cidade'] ?: null]);
            header('Location: login.php?sucesso=1'); exit;
        } catch (PDOException $ex) {
            if ($ex->getCode() === '23000') {
                $erros['geral'] = 'E-mail ou CPF já cadastrados.';
                if (stripos($ex->getMessage(), 'cpf') !== false) $erros['cpf'] = 'Este CPF já está cadastrado.';
                else $erros['email'] = 'Este e-mail já está cadastrado. <a href="login.php">Entrar?</a>';
            } else $erros['geral'] = 'Não foi possível concluir o cadastro agora. Tente novamente em instantes.';
        }
    }
}
function campo_erro(array $erros, string $k): string {
    return isset($erros[$k]) ? '<small class="erro" id="e-'.$k.'" role="alert">'.($k==='email' ? $erros[$k] : e($erros[$k])).'</small>' : '';
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crie sua conta — Ecoleta</title>
  <link rel="stylesheet" href="css/auth.css">
</head>
<body class="cad">
<aside class="lado">
  <a class="marca" href="index.php">♻️ Ecoleta</a>
  <h1>Comece a transformar reciclagem em experiências.</h1>
  <ul>
    <li><b>Ganhe pontos</b> a cada entrega nos pontos de coleta</li>
    <li><b>Troque</b> por cinema, shows e bem-estar</li>
    <li><b>Acompanhe</b> seu histórico e seu impacto</li>
  </ul>
</aside>
<main>
  <form class="card" method="post" action="cadastro.php" id="form-cadastro" novalidate>
    <h2>Crie sua conta</h2>
    <p class="sub">Leva menos de um minuto. Já tem conta? <a href="login.php">Entrar</a></p>
    <?php if (isset($erros['geral'])): ?><div class="alerta" role="alert"><?= e($erros['geral']) ?></div><?php endif; ?>
    <?= csrf_campo() ?>

    <div class="campo cheio">
      <label for="nome">Nome completo</label>
      <input id="nome" name="nome" autocomplete="name" required maxlength="120" value="<?= e($v['nome']) ?>" placeholder="Maria Clara Silva">
      <?= campo_erro($erros,'nome') ?>
    </div>
    <div class="campo cheio">
      <label for="email">E-mail</label>
      <input id="email" name="email" type="email" autocomplete="email" required maxlength="160" value="<?= e($v['email']) ?>" placeholder="voce@email.com">
      <?= campo_erro($erros,'email') ?>
    </div>
    <div class="campo">
      <label for="cpf">CPF</label>
      <input id="cpf" name="cpf" inputmode="numeric" data-mascara="cpf" required maxlength="14" value="<?= e($v['cpf']) ?>" placeholder="000.000.000-00">
      <?= campo_erro($erros,'cpf') ?>
    </div>
    <div class="campo">
      <label for="telefone">Telefone</label>
      <input id="telefone" name="telefone" type="tel" inputmode="tel" data-mascara="telefone" autocomplete="tel" required maxlength="15" value="<?= e($v['telefone']) ?>" placeholder="(81) 99999-0000">
      <?= campo_erro($erros,'telefone') ?>
    </div>
    <div class="campo">
      <label for="nascimento">Nascimento <em>(opcional)</em></label>
      <input id="nascimento" name="nascimento" type="date" autocomplete="bday" max="<?= date('Y-m-d') ?>" value="<?= e($v['nascimento']) ?>">
      <?= campo_erro($erros,'nascimento') ?>
    </div>
    <div class="campo">
      <label for="cidade">Cidade <em>(opcional)</em></label>
      <input id="cidade" name="cidade" autocomplete="address-level2" maxlength="80" value="<?= e($v['cidade']) ?>" placeholder="Recife">
    </div>
    <div class="campo">
      <label for="senha">Senha</label>
      <div class="senha-box"><input id="senha" name="senha" type="password" autocomplete="new-password" required minlength="8"><button type="button" class="ver" data-alvo="senha" aria-label="Mostrar senha">Mostrar</button></div>
      <div class="forca" id="forca" aria-hidden="true"><i></i></div><small id="forca-txt" class="dica">Mínimo de 8 caracteres, com letras e números.</small>
      <?= campo_erro($erros,'senha') ?>
    </div>
    <div class="campo">
      <label for="confirma">Repita a senha</label>
      <div class="senha-box"><input id="confirma" name="confirma" type="password" autocomplete="new-password" required><button type="button" class="ver" data-alvo="confirma" aria-label="Mostrar senha">Mostrar</button></div>
      <?= campo_erro($erros,'confirma') ?>
    </div>
    <div class="campo cheio termos">
      <label class="check"><input type="checkbox" name="termos" value="1" <?= !empty($_POST['termos']) ? 'checked' : '' ?>> Li e aceito os termos de uso e a política de privacidade.</label>
      <?= campo_erro($erros,'termos') ?>
    </div>
    <button class="enviar cheio" type="submit">Criar conta</button>
  </form>
</main>
<script src="js/mascaras.js"></script>
<script src="js/cadastro.js"></script>
</body>
</html>
