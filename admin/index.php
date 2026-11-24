<?php
require __DIR__.'/../includes/auth.php';
$eu = exigir_admin();
$pdo = db();

$E = [
 'materiais' => ['t'=>'Materiais','ativo'=>1,'cols'=>['icone'=>'Ícone','nome'=>'Nome','pontos_por_kg'=>'Pts/kg'],
   'f'=>[['nome','Nome','text',1],['pontos_por_kg','Pontos por kg','number',1],['icone','Ícone (emoji)','text',0],['descricao','Descrição','text',0]]],
 'parceiros' => ['t'=>'Parceiros','ativo'=>1,'cols'=>['nome'=>'Nome','categoria'=>'Categoria','cidade'=>'Cidade'],
   'f'=>[['nome','Nome','text',1],['categoria','Categoria','text',0],['cidade','Cidade','text',0],['endereco','Endereço','text',0],['telefone','Telefone','text',0]]],
 'beneficios' => ['t'=>'Benefícios','ativo'=>1,'cols'=>['titulo'=>'Título','custo_pontos'=>'Pontos','estoque'=>'Estoque','validade'=>'Validade'],
   'f'=>[['titulo','Título','text',1],['custo_pontos','Custo em pontos','number',1],['parceiro_id','Parceiro','select:@parceiros',0],['estoque','Estoque','number',0],['validade','Validade','date',0],['descricao','Descrição','text',0]]],
 'usuarios' => ['t'=>'Usuários','ativo'=>0,'cols'=>['nome'=>'Nome','email'=>'E-mail','perfil'=>'Perfil','pontos'=>'Pontos'],
   'f'=>[['nome','Nome','text',1],['email','E-mail','email',1],['cpf','CPF','text',1],['telefone','Telefone','text',1],['senha','Senha (mín. 8)','password',1],['perfil','Perfil','select:usuario,admin',1]]],
];
$tab = isset($E[$_GET['e'] ?? '']) ? $_GET['e'] : 'materiais';
$cfg = $E[$tab]; $msg = $err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if (!csrf_ok()) throw new RuntimeException('Sessão expirada. Recarregue a página.');
        if ($acao === 'criar') {
            $cols = $vals = [];
            foreach ($cfg['f'] as [$n,$rot,$tipo,$obr]) {
                $x = trim($_POST[$n] ?? '');
                if ($x === '' && $obr) throw new RuntimeException("Preencha: $rot.");
                if ($tipo === 'number' && $x !== '' && (!ctype_digit($x))) throw new RuntimeException("$rot deve ser um número inteiro.");
                if ($tipo === 'email' && !filter_var($x, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('E-mail inválido.');
                if ($tipo === 'date' && $x !== '' && !DateTime::createFromFormat('Y-m-d', $x)) throw new RuntimeException("$rot inválida.");
                if (str_starts_with($tipo,'select:') && !str_starts_with($tipo,'select:@') && !in_array($x, explode(',', substr($tipo,7)), true)) throw new RuntimeException("$rot inválido.");
                if ($tab === 'usuarios' && $n === 'cpf') { if (!cpf_valido($x)) throw new RuntimeException('CPF inválido.'); $x = formatar_cpf($x); }
                if ($tab === 'usuarios' && $n === 'telefone') { if (!telefone_valido($x)) throw new RuntimeException('Telefone inválido (use DDD + número).'); $x = formatar_telefone($x); }
                if ($n === 'senha') { if (strlen($x) < 8) throw new RuntimeException('A senha precisa de 8+ caracteres.'); $n = 'senha_hash'; $x = password_hash($x, PASSWORD_DEFAULT); }
                $cols[] = $n; $vals[] = $x === '' ? null : $x;
            }
            $pdo->prepare("INSERT INTO $tab (".implode(',',$cols).') VALUES ('.implode(',',array_fill(0,count($cols),'?')).')')->execute($vals);
            $msg = 'Cadastrado com sucesso.';
        } elseif ($acao === 'excluir') {
            if ($tab === 'usuarios' && $id === (int)$eu['id']) throw new RuntimeException('Você não pode excluir a si mesmo.');
            $pdo->prepare("DELETE FROM $tab WHERE id=?")->execute([$id]); $msg = 'Removido.';
        } elseif ($acao === 'alternar' && $cfg['ativo']) {
            $pdo->prepare("UPDATE $tab SET ativo=1-ativo WHERE id=?")->execute([$id]);
        }
    } catch (RuntimeException $x) { $err = $x->getMessage();
    } catch (PDOException $x) { $err = $x->getCode() === '23000' ? 'Registro duplicado ou em uso por outros dados (desative em vez de excluir).' : 'Erro no banco de dados.'; }
}
$linhas = $pdo->query("SELECT * FROM $tab ORDER BY id DESC LIMIT 200")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administração — Ecoleta</title><link rel="stylesheet" href="../css/auth.css"></head>
<body><div class="adm">
<header><h1>Administração</h1><span><?= e($eu['nome']) ?> · <a href="../inicio.php">Voltar ao site</a> · <a href="../logout.php">Sair</a></span></header>
<nav class="abas"><?php foreach ($E as $k=>$c): ?><a href="?e=<?= $k ?>" class="<?= $k===$tab?'on':'' ?>"><?= e($c['t']) ?></a><?php endforeach; ?></nav>
<?php if ($msg): ?><div class="ok"><?= e($msg) ?></div><?php endif; ?>
<?php if ($err): ?><div class="alerta" role="alert"><?= e($err) ?></div><?php endif; ?>

<form method="post" class="card"><h2>Cadastrar em <?= e($cfg['t']) ?></h2><?= csrf_campo() ?><input type="hidden" name="acao" value="criar">
<?php foreach ($cfg['f'] as [$n,$rot,$tipo,$obr]): ?>
 <div class="campo"><label for="f-<?= $n ?>"><?= e($rot) ?></label>
 <?php if (str_starts_with($tipo,'select:')): $o = substr($tipo,7);
   $ops = $o[0]==='@' ? $pdo->query('SELECT id v, nome l FROM '.substr($o,1).' WHERE ativo=1 ORDER BY nome')->fetchAll() : array_map(fn($x)=>['v'=>$x,'l'=>ucfirst($x)], explode(',',$o)); ?>
   <select id="f-<?= $n ?>" name="<?= $n ?>" <?= $obr?'required':'' ?>><?php if(!$obr): ?><option value="">—</option><?php endif; foreach ($ops as $op): ?><option value="<?= e($op['v']) ?>"><?= e($op['l']) ?></option><?php endforeach; ?></select>
 <?php else: ?><input id="f-<?= $n ?>" name="<?= $n ?>" type="<?= $tipo ?>" <?= $tipo==='number'?'min="0"':'' ?> <?= $obr?'required':'' ?> <?= $tipo==='password'?'autocomplete="new-password"':'' ?>><?php endif; ?></div>
<?php endforeach; ?>
<button class="enviar cheio" type="submit">Cadastrar</button></form>

<div class="tabela"><table><thead><tr><th>#</th><?php foreach ($cfg['cols'] as $r): ?><th><?= e($r) ?></th><?php endforeach; ?><?php if ($cfg['ativo']): ?><th>Status</th><?php endif; ?><th></th></tr></thead><tbody>
<?php foreach ($linhas as $l): ?><tr><td><?= (int)$l['id'] ?></td>
 <?php foreach ($cfg['cols'] as $c=>$_): ?><td><?= e($l[$c] ?? '—') ?></td><?php endforeach; ?>
 <?php if ($cfg['ativo']): ?><td><form method="post"><?= csrf_campo() ?><input type="hidden" name="acao" value="alternar"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><button class="mini"><?= $l['ativo'] ? 'Ativo' : 'Inativo' ?></button></form></td><?php endif; ?>
 <td><form method="post" onsubmit="return confirm('Excluir este registro?')"><?= csrf_campo() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$l['id'] ?>"><button class="mini del">Excluir</button></form></td></tr>
<?php endforeach; if (!$linhas): ?><tr><td colspan="9">Nada cadastrado ainda.</td></tr><?php endif; ?></tbody></table></div>
</div></body></html>
