<?php
require_once 'config/conexao.php';
require_once 'config/auth.php';
exigir_login();
$u = $_SESSION['usuario'];
$tit = ['administrador' => 'Administrador', 'gerente' => 'Gerente', 'funcionario' => 'Funcionário'];
$clientes = ['clientes.php', 'Clientes', 'Cadastre, consulte e veja o histórico dos clientes.'];
$pedidos = ['pedidos.php', 'Pedidos gráficos', 'Consulte, edite e acompanhe os pedidos até a entrega.'];
$novoPedido = ['novo_pedido.php', 'Registrar pedido gráfico', 'Registre um serviço solicitado por um cliente.'];
$novaVenda = ['nova_venda.php', 'Registrar venda', 'Venda de produtos ou faturamento de pedido gráfico.'];
$links = [
    'administrador' => [['usuarios.php', 'Gerenciar usuários', 'Cadastre, edite e desative acessos.'], ['estoque.php', 'Estoque', 'Produtos, entradas, saídas e histórico.'], $clientes, $novoPedido, $pedidos, $novaVenda, ['vendas.php', 'Consultar vendas', 'Acompanhe vendas, pedidos e cancelamentos.'], ['relatorios.php', 'Relatórios gerenciais', 'Consulte indicadores e gere PDF para a gerência.']],
    'gerente' => [$clientes, $novoPedido, $pedidos, ['estoque.php', 'Gerenciar estoque', 'Cadastrar produtos e atualizar quantidades.'], $novaVenda, ['vendas.php', 'Acompanhar vendas', 'Consultar vendas e atualizar status.'], ['relatorios.php', 'Relatórios gerenciais', 'Consulte indicadores e gere PDF para a gerência.']],
    'funcionario' => [$clientes, $novoPedido, ['pedidos.php', 'Acompanhar pedidos', 'Consulte os pedidos e atualize o andamento.'], $novaVenda, ['vendas.php', 'Minhas vendas', 'Consultar vendas cadastradas por você.']],
];
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Painel | Gráfica VAV</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><div><div class="logo small">GV</div><span class="eyebrow">SISTEMA INTERNO</span></div><div class="userbox">Olá, <strong><?= h($u['nome']) ?></strong><span class="tag"><?= h($tit[$u['nivel']] ?? $u['nivel']) ?></span><a class="link-sair" href="logout.php">Sair</a></div></header><section class="content"><?php mostrar_flash(); ?><h1>Seu painel</h1><p class="subtitle">Selecione uma área para começar.</p><div class="cards"><?php foreach ($links[$u['nivel']] ?? [] as $l): ?><a class="module-card" href="<?= h($l[0]) ?>"><h2><?= h($l[1]) ?></h2><p><?= h($l[2]) ?></p><span>Entrar →</span></a><?php endforeach; ?></div></section></main></body></html>
