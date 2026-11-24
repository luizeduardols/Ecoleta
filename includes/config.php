<?php
$cfg = ['host'=>'localhost','porta'=>3306,'banco'=>'ecoleta','usuario'=>'root','senha'=>''];
if (is_file(__DIR__.'/config.local.php')) { $cfg = array_merge($cfg, require __DIR__.'/config.local.php'); }
return $cfg;
