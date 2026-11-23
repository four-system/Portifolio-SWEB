<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_login();
$nivel = $_SESSION['usuario']['nivel'];
$podeExcluir = in_array($nivel, ['administrador', 'gerente'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $acao = $_POST['acao'] ?? ''; $id = (int)($_POST['id'] ?? 0);
    try {
        if ($acao === 'salvar') {
            $nome = trim($_POST['nome'] ?? ''); $telefone = trim($_POST['telefone'] ?? ''); $email = trim($_POST['email'] ?? '');
            if ($nome === '') throw new Exception('Informe o nome ou razão social.');
            if (mb_strlen($nome) > 150 || mb_strlen($email) > 150 || mb_strlen($telefone) > 30) throw new Exception('Algum campo excede o tamanho permitido.');
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Informe um e-mail válido.');
            if ($telefone !== '' && !preg_match('/^[0-9()+\-\s.]{8,30}$/', $telefone)) throw new Exception('Informe um telefone válido (somente números, espaços, parênteses, + e -).');
            if ($email !== '') {
                $q = $pdo->prepare('SELECT id FROM clientes WHERE email=? AND id<>? LIMIT 1'); $q->execute([$email, $id]);
                if ($q->fetch()) throw new Exception('Já existe outro cliente com este e-mail.');
            }
            if ($id) {
                $q = $pdo->prepare('SELECT id FROM clientes WHERE id=?'); $q->execute([$id]);
                if (!$q->fetch()) throw new Exception('Cliente não encontrado.');
                $pdo->prepare('UPDATE clientes SET nome=?,telefone=?,email=? WHERE id=?')->execute([$nome, $telefone ?: null, $email ?: null, $id]);
                flash('Cliente atualizado.');
            } else {
                $pdo->prepare('INSERT INTO clientes(nome,telefone,email) VALUES(?,?,?)')->execute([$nome, $telefone ?: null, $email ?: null]);
                flash('Cliente cadastrado.');
            }
        } elseif ($acao === 'excluir') {
            if (!$podeExcluir) { http_response_code(403); exit('Acesso não autorizado.'); }
            $q = $pdo->prepare('SELECT (SELECT COUNT(*) FROM pedidos WHERE cliente_id=?) AS ped, (SELECT COUNT(*) FROM vendas WHERE cliente_id=?) AS ven');
            $q->execute([$id, $id]); $uso = $q->fetch();
            if ((int)$uso['ped'] > 0 || (int)$uso['ven'] > 0) throw new Exception('Este cliente possui pedidos ou vendas e não pode ser excluído (isso apagaria o histórico). Edite os dados se precisar corrigir algo.');
            $pdo->prepare('DELETE FROM clientes WHERE id=?')->execute([$id]);
            flash('Cliente excluído.');
        }
    } catch (PDOException $e) {
        error_log('Clientes: ' . $e->getMessage()); flash_erro('Não foi possível salvar. Se o problema persistir, avise o administrador.');
    } catch (Throwable $e) { flash_erro($e->getMessage()); }
    header('Location: clientes.php'); exit;
}

$edit = null; if (isset($_GET['editar'])) { $q = $pdo->prepare('SELECT * FROM clientes WHERE id=?'); $q->execute([(int)$_GET['editar']]); $edit = $q->fetch() ?: null; }
$hist = null; $histPed = $histVen = [];
if (isset($_GET['historico'])) {
    $q = $pdo->prepare('SELECT * FROM clientes WHERE id=?'); $q->execute([(int)$_GET['historico']]); $hist = $q->fetch() ?: null;
    if ($hist) {
        $q = $pdo->prepare('SELECT id,servico,quantidade,data_pedido,prazo,valor,status FROM pedidos WHERE cliente_id=? ORDER BY data_pedido DESC,id DESC'); $q->execute([$hist['id']]); $histPed = $q->fetchAll();
        $q = $pdo->prepare('SELECT v.id,v.tipo,v.total,v.status,v.pedido_id,COALESCE(v.data_venda,DATE(v.criado_em)) AS data FROM vendas v LEFT JOIN pedidos p ON p.id=v.pedido_id WHERE v.cliente_id=? OR p.cliente_id=? ORDER BY data DESC,v.id DESC'); $q->execute([$hist['id'], $hist['id']]); $histVen = $q->fetchAll();
    }
}
$busca = trim($_GET['busca'] ?? '');
if ($busca !== '') { $q = $pdo->prepare('SELECT * FROM clientes WHERE nome LIKE ? OR telefone LIKE ? OR email LIKE ? ORDER BY nome'); $like = '%' . $busca . '%'; $q->execute([$like, $like, $like]); $clientes = $q->fetchAll(); }
else $clientes = $pdo->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
require_once 'config/relatorios_dados.php';
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Clientes</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Cadastro de clientes</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash(); ?>
<?php if ($hist): ?>
<h1>Histórico de <?= h($hist['nome']) ?></h1><p class="subtitle"><?= h($hist['telefone'] ?: 'Sem telefone') ?> · <?= h($hist['email'] ?: 'Sem e-mail') ?> · <a href="clientes.php">← Voltar à lista</a></p>
<h2>Pedidos gráficos</h2><div class="table-wrap"><table><thead><tr><th>Nº</th><th>Serviço</th><th>Qtd.</th><th>Data</th><th>Prazo</th><th>Valor</th><th>Status</th></tr></thead><tbody><?php foreach ($histPed as $p): ?><tr><td>#<?= (int)$p['id'] ?></td><td><?= h($p['servico']) ?></td><td><?= h($p['quantidade']) ?></td><td><?= date('d/m/Y', strtotime($p['data_pedido'])) ?></td><td><?= date('d/m/Y', strtotime($p['prazo'])) ?></td><td>R$ <?= number_format((float)$p['valor'], 2, ',', '.') ?></td><td><?= h(rotulo_status_pedido($p['status'])) ?></td></tr><?php endforeach; ?><?php if (!$histPed): ?><tr><td colspan="7">Nenhum pedido para este cliente.</td></tr><?php endif; ?></tbody></table></div>
<h2>Compras e faturamentos</h2><div class="table-wrap"><table><thead><tr><th>Nº</th><th>Tipo</th><th>Pedido</th><th>Data</th><th>Total</th><th>Status</th></tr></thead><tbody><?php foreach ($histVen as $v): ?><tr><td>#<?= (int)$v['id'] ?></td><td><?= $v['tipo'] === 'servico' ? 'Serviço gráfico' : 'Venda direta' ?></td><td><?= $v['pedido_id'] ? '#' . (int)$v['pedido_id'] : '—' ?></td><td><?= date('d/m/Y', strtotime($v['data'])) ?></td><td>R$ <?= number_format((float)$v['total'], 2, ',', '.') ?></td><td><?= h(rotulo_status_venda($v['status'])) ?></td></tr><?php endforeach; ?><?php if (!$histVen): ?><tr><td colspan="6">Nenhuma compra registrada para este cliente.</td></tr><?php endif; ?></tbody></table></div>
<?php else: ?>
<h1><?= $edit ? 'Editar cliente' : 'Novo cliente' ?></h1><form class="form-box" method="post"><?= csrf_field() ?><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?= h($edit['id'] ?? '') ?>"><label>Nome ou razão social *</label><input name="nome" required maxlength="150" value="<?= h($edit['nome'] ?? '') ?>"><div class="form-row"><div><label>Telefone</label><input name="telefone" maxlength="30" value="<?= h($edit['telefone'] ?? '') ?>"></div><div><label>E-mail</label><input type="email" name="email" maxlength="150" value="<?= h($edit['email'] ?? '') ?>"></div></div><button>Salvar cliente</button><?php if ($edit): ?> <a class="button-link" href="clientes.php">Cancelar</a><?php endif; ?></form>
<h2>Clientes cadastrados</h2><form method="get" class="inline"><input name="busca" placeholder="Buscar nome, telefone ou e-mail" value="<?= h($busca) ?>"><button class="mini">Buscar</button><a href="clientes.php">Limpar</a></form><div class="table-wrap"><table><thead><tr><th>Nome / Razão social</th><th>Telefone</th><th>E-mail</th><th>Ação</th></tr></thead><tbody><?php foreach ($clientes as $c): ?><tr><td><?= h($c['nome']) ?></td><td><?= h($c['telefone'] ?: '—') ?></td><td><?= h($c['email'] ?: '—') ?></td><td><a href="?editar=<?= (int)$c['id'] ?>">Editar</a> · <a href="?historico=<?= (int)$c['id'] ?>">Histórico</a><?php if ($podeExcluir): ?><form class="inline" method="post" onsubmit="return confirm('Excluir este cliente? Só é possível se ele não tiver pedidos ou vendas.')"><?= csrf_field() ?><input type="hidden" name="acao" value="excluir"><input type="hidden" name="id" value="<?= (int)$c['id'] ?>"><button class="mini perigo">Excluir</button></form><?php endif; ?></td></tr><?php endforeach; ?><?php if (!$clientes): ?><tr><td colspan="4">Nenhum cliente encontrado.</td></tr><?php endif; ?></tbody></table></div>
<?php endif; ?>
</section></main></body></html>
