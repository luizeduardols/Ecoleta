<?php
// Configuração padrão (XAMPP/Laragon). Para sobrescrever, crie includes/config.local.php
// retornando um array com as mesmas chaves (esse arquivo não vai para o Git).
$cfg = ['host'=>'localhost','porta'=>3306,'banco'=>'ppr','usuario'=>'root','senha'=>''];
if (is_file(__DIR__.'/config.local.php')) { $cfg = array_merge($cfg, require __DIR__.'/config.local.php'); }
return $cfg;
