<?php
function exigir_login(): void { if (empty($_SESSION['usuario'])) { header('Location: index.php'); exit; } }
function exigir_cargo(array $cargos): void { exigir_login(); if (!in_array($_SESSION['usuario']['nivel'], $cargos, true)) { http_response_code(403); exit('Acesso não autorizado. <a href="painel.php">Voltar</a>'); } }
function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function flash($msg): void { $_SESSION['flash'] = $msg; }
function mostrar_flash(): void { if (!empty($_SESSION['flash'])) { echo '<div class="alerta sucesso">'.h($_SESSION['flash']).'</div>'; unset($_SESSION['flash']); } }
