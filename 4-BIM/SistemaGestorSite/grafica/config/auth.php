<?php
/**
 * Sessão, cabeçalhos de segurança, proteção CSRF, controle de acesso e mensagens flash.
 * Este arquivo inicia a sessão: as páginas NÃO devem chamar session_start() antes dele.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_set_cookie_params(['lifetime' => 0, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
if (!headers_sent()) {
    header('X-Frame-Options: SAMEORIGIN');
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: same-origin');
}

function h($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/* ---------- Mensagens flash ---------- */
function flash(string $msg, string $tipo = 'sucesso'): void { $_SESSION['flash'] = ['msg' => $msg, 'tipo' => $tipo]; }
function flash_erro(string $msg): void { flash($msg, 'erro'); }
function mostrar_flash(): void {
    if (empty($_SESSION['flash'])) return;
    $f = $_SESSION['flash'];
    if (!is_array($f)) $f = ['msg' => (string)$f, 'tipo' => 'sucesso'];
    echo '<div class="alerta ' . ($f['tipo'] === 'erro' ? 'erro' : 'sucesso') . '" role="alert">' . h($f['msg']) . '</div>';
    unset($_SESSION['flash']);
}

/* ---------- CSRF ---------- */
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . h(csrf_token()) . '">'; }
/** Deve ser chamada no início de todo tratamento de POST. */
function csrf_verificar(): void {
    if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') return;
    $t = $_POST['csrf'] ?? '';
    if (!is_string($t) || empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], $t)) {
        http_response_code(419);
        exit('Sessão expirada ou requisição inválida. <a href="index.php">Voltar ao início</a>');
    }
}

/* ---------- Controle de acesso ---------- */
function exigir_login(): void {
    if (empty($_SESSION['usuario'])) { header('Location: index.php'); exit; }
    // Revalida no banco: usuário desativado perde o acesso imediatamente e mudanças de cargo valem na hora.
    global $pdo;
    if (isset($pdo) && $pdo instanceof PDO) {
        try {
            $q = $pdo->prepare('SELECT nome,nivel,ativo FROM usuarios WHERE id=?');
            $q->execute([(int)$_SESSION['usuario']['id']]);
            $u = $q->fetch();
            if (!$u || !(int)$u['ativo']) {
                $_SESSION = [];
                $_SESSION['erro_login'] = 'Seu acesso foi desativado. Procure o administrador.';
                header('Location: index.php'); exit;
            }
            $_SESSION['usuario']['nivel'] = $u['nivel'];
            $_SESSION['usuario']['nome'] = $u['nome'];
        } catch (PDOException $e) { error_log('auth: ' . $e->getMessage()); }
    }
}
function exigir_cargo(array $cargos): void {
    exigir_login();
    if (!in_array($_SESSION['usuario']['nivel'], $cargos, true)) {
        http_response_code(403);
        exit('Acesso não autorizado. <a href="painel.php">Voltar</a>');
    }
}

/* ---------- Validações comuns ---------- */
function data_valida(string $d): bool {
    $x = DateTime::createFromFormat('Y-m-d', $d);
    return $x && $x->format('Y-m-d') === $d;
}
function num_post($v): float { return (float)str_replace(',', '.', trim((string)$v)); }
