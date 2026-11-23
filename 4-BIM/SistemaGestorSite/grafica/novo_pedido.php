<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_login();

// Modo edição: novo_pedido.php?editar=ID
$editId = (int)($_GET['editar'] ?? $_POST['id'] ?? 0);
$edit = null; $faturado = false;
if ($editId) {
    $q = $pdo->prepare('SELECT * FROM pedidos WHERE id=?'); $q->execute([$editId]); $edit = $q->fetch() ?: null;
    if (!$edit) { flash_erro('Pedido não encontrado.'); header('Location: pedidos.php'); exit; }
    $q = $pdo->prepare('SELECT id FROM vendas WHERE pedido_id=?'); $q->execute([$editId]); $faturado = (bool)$q->fetch();
    if ($edit['status'] === 'cancelado') { flash_erro('Pedido cancelado não pode ser editado.'); header('Location: pedidos.php'); exit; }
}
$clientes = $pdo->query('SELECT id,nome FROM clientes ORDER BY nome')->fetchAll();
$funcionarios = $pdo->query("SELECT id,nome FROM usuarios WHERE ativo=1 ORDER BY nome")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_verificar();
    $cliente = (int)($_POST['cliente_id'] ?? 0); $servico = trim($_POST['servico'] ?? ''); $material = trim($_POST['descricao_material'] ?? '');
    $qtd = num_post($_POST['quantidade'] ?? 0); $data = $_POST['data_pedido'] ?? date('Y-m-d'); $prazo = $_POST['prazo'] ?? '';
    $valor = num_post($_POST['valor'] ?? '0'); $responsavel = (int)($_POST['funcionario_id'] ?? 0);
    $q = $pdo->prepare('SELECT id FROM clientes WHERE id=?'); $q->execute([$cliente]); $clienteOk = $q->fetch();
    $q = $pdo->prepare('SELECT id FROM usuarios WHERE id=? AND ativo=1'); $q->execute([$responsavel]); $funcOk = $q->fetch();
    if (!$clienteOk || !$funcOk || $servico === '' || $material === '' || $qtd <= 0 || $valor < 0) {
        flash_erro('Confira os campos: cliente, serviço, material, quantidade, valor e responsável.');
    } elseif (!data_valida($data) || !data_valida($prazo)) {
        flash_erro('Informe datas válidas para o pedido e para o prazo.');
    } elseif ($prazo < $data) {
        flash_erro('O prazo combinado não pode ser anterior à data do pedido.');
    } elseif (mb_strlen($servico) > 150 || $qtd > 9999999999 || $valor > 9999999999) {
        flash_erro('Algum campo excede o tamanho ou valor permitido.');
    } elseif ($edit && $faturado && abs($valor - (float)$edit['valor']) > 0.001) {
        flash_erro('Este pedido já foi faturado: o valor não pode ser alterado. Cancele a venda antes, se necessário.');
    } else {
        try {
            if ($edit) {
                $pdo->prepare('UPDATE pedidos SET cliente_id=?,servico=?,descricao_material=?,quantidade=?,data_pedido=?,prazo=?,valor=?,funcionario_id=? WHERE id=?')
                    ->execute([$cliente, $servico, $material, $qtd, $data, $prazo, $valor, $responsavel, $editId]);
                flash('Pedido #' . $editId . ' atualizado.');
            } else {
                $pdo->prepare("INSERT INTO pedidos(cliente_id,servico,descricao_material,quantidade,data_pedido,prazo,valor,funcionario_id,status) VALUES(?,?,?,?,?,?,?,?,'recebido')")
                    ->execute([$cliente, $servico, $material, $qtd, $data, $prazo, $valor, $responsavel]);
                flash('Pedido #' . $pdo->lastInsertId() . ' registrado com sucesso.');
            }
            header('Location: pedidos.php'); exit;
        } catch (PDOException $e) {
            error_log('Pedido: ' . $e->getMessage()); flash_erro('Não foi possível salvar o pedido. Avise o administrador.');
        }
    }
}
// Valores exibidos: o que foi enviado (em caso de erro) > pedido em edição > padrão.
$v = fn($k, $pad = '') => $_POST[$k] ?? ($edit[$k] ?? $pad);
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= $edit ? 'Editar pedido' : 'Novo pedido' ?></title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2><?= $edit ? 'Editar pedido #' . (int)$editId : 'Registrar pedido gráfico' ?></h2><a class="link-sair" href="pedidos.php">← Pedidos</a></header><section class="content"><?php mostrar_flash(); ?><form class="form-box" method="post"><?= csrf_field() ?><?php if ($edit): ?><input type="hidden" name="id" value="<?= (int)$editId ?>"><?php endif; ?><label>Cliente *</label><select name="cliente_id" required><option value="">Selecione um cliente</option><?php foreach ($clientes as $c): ?><option value="<?= (int)$c['id'] ?>" <?= ((int)$v('cliente_id', 0) === (int)$c['id']) ? 'selected' : '' ?>><?= h($c['nome']) ?></option><?php endforeach; ?></select><?php if (!$clientes): ?><p class="alerta erro">Cadastre um cliente antes de registrar pedidos. <a href="clientes.php">Cadastrar cliente</a></p><?php endif; ?><label>Serviço solicitado *</label><input name="servico" required maxlength="150" value="<?= h($v('servico')) ?>" placeholder="Ex.: Cartões de visita, banners, folders"><label>Descrição do material (medidas, papel, acabamento) *</label><textarea name="descricao_material" required><?= h($v('descricao_material')) ?></textarea><div class="form-row"><div><label>Quantidade *</label><input type="number" name="quantidade" min="0.01" step="0.01" required value="<?= h($v('quantidade', '1')) ?>"></div><div><label>Data do pedido *</label><input type="date" name="data_pedido" required value="<?= h($v('data_pedido', date('Y-m-d'))) ?>"></div><div><label>Prazo combinado *</label><input type="date" name="prazo" required value="<?= h($v('prazo')) ?>"></div></div><div class="form-row"><div><label>Valor (R$) *</label><input type="number" name="valor" min="0" step="0.01" required value="<?= h($v('valor', '0.00')) ?>" <?= $faturado ? 'readonly title="Pedido já faturado"' : '' ?>></div><div><label>Funcionário responsável *</label><select name="funcionario_id" required><option value="">Selecione</option><?php foreach ($funcionarios as $f): ?><option value="<?= (int)$f['id'] ?>" <?= ((int)$v('funcionario_id', $_SESSION['usuario']['id']) === (int)$f['id']) ? 'selected' : '' ?>><?= h($f['nome']) ?></option><?php endforeach; ?></select></div></div><button <?= !$clientes || !$funcionarios ? 'disabled' : '' ?>><?= $edit ? 'Salvar alterações' : 'Registrar pedido' ?></button></form></section></main></body></html>
