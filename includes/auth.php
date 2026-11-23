<?php
require_once __DIR__.'/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
    session_start();
}
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function iniciais(string $nome): string {
    $p = preg_split('/\s+/', trim($nome));
    return mb_strtoupper(mb_substr($p[0],0,1).(count($p)>1 ? mb_substr(end($p),0,1) : ''));
}
function csrf_token(): string {
    return $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
}
function csrf_campo(): string { return '<input type="hidden" name="csrf" value="'.e(csrf_token()).'">'; }
function csrf_ok(): bool {
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function usuario_atual(): ?array {
    if (empty($_SESSION['uid'])) return null;
    static $u = false;
    if ($u === false) {
        $st = db()->prepare('SELECT id,nome,email,cpf,telefone,perfil,pontos,criado_em FROM usuarios WHERE id=?');
        $st->execute([$_SESSION['uid']]);
        $u = $st->fetch() ?: null;
    }
    return $u;
}
function exigir_login(): array {
    $u = usuario_atual();
    if (!$u) { header('Location: login.php'); exit; }
    return $u;
}
function exigir_admin(): array {
    $u = usuario_atual();
    if (!$u) { header('Location: ../login.php'); exit; }
    if ($u['perfil'] !== 'admin') { http_response_code(403); exit('Acesso restrito a administradores.'); }
    return $u;
}
function entrar(int $id, bool $lembrar = false): void {
    session_regenerate_id(true);
    $_SESSION['uid'] = $id;
    if ($lembrar) setcookie(session_name(), session_id(), ['expires'=>time()+60*60*24*30,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
}

// ---- CPF, telefone e máscaras de exibição (guia: ma*****@dominio, 123.***.***-09)
function so_digitos(string $s): string { return preg_replace('/\D/', '', $s); }
function cpf_valido(string $c): bool {
    $c = so_digitos($c);
    if (strlen($c) !== 11 || preg_match('/^(\d)\1{10}$/', $c)) return false;
    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $i = 0; $i < $t; $i++) $d += (int)$c[$i] * (($t + 1) - $i);
        if ((int)$c[$t] !== (($d * 10) % 11) % 10) return false;
    }
    return true;
}
function formatar_cpf(string $c): string {
    return preg_replace('/^(\d{3})(\d{3})(\d{3})(\d{2})$/', '$1.$2.$3-$4', so_digitos($c));
}
function telefone_valido(string $t): bool { return preg_match('/^[1-9]\d{9,10}$/', so_digitos($t)) === 1; }
function formatar_telefone(string $t): string {
    $d = so_digitos($t);
    return strlen($d) === 11 ? preg_replace('/^(\d{2})(\d{5})(\d{4})$/', '($1) $2-$3', $d)
                             : preg_replace('/^(\d{2})(\d{4})(\d{4})$/', '($1) $2-$3', $d);
}
function mascarar_email(string $e): string { $p = strstr($e, '@'); return mb_substr($e, 0, 2).'*****'.($p === false ? '' : $p); }
function mascarar_cpf(?string $c): string { return $c ? substr($c, 0, 4).'***.***-'.substr($c, -2) : 'não informado'; }
