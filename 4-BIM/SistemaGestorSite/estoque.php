<?php
session_start(); require_once 'config/conexao.php'; require_once 'config/auth.php'; exigir_cargo(['administrador','gerente']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    try {
        if ($acao === 'salvar') {
            $id=(int)($_POST['id']??0); $nome=trim($_POST['nome']??''); $codigo=trim($_POST['codigo']??'');
            $categoria=trim($_POST['categoria']??''); $desc=trim($_POST['descricao']??''); $un=trim($_POST['unidade']??'un');
            $qtd=(float)str_replace(',','.',$_POST['quantidade']??'0'); $min=(float)str_replace(',','.',$_POST['estoque_minimo']??'0');
            $preco=(float)str_replace(',','.',$_POST['preco']??'0');
            if ($nome==='' || $un==='' || $qtd<0 || $min<0 || $preco<0) throw new Exception('Informe nome, unidade e valores válidos (não negativos).');
            if ($codigo!=='') { $q=$pdo->prepare('SELECT id FROM produtos WHERE codigo=? AND id<>?'); $q->execute([$codigo,$id]); if($q->fetch()) throw new Exception('Este código já está cadastrado em outro produto.'); }
            if ($id) {
                $pdo->beginTransaction();
                $q=$pdo->prepare('SELECT quantidade FROM produtos WHERE id=? FOR UPDATE'); $q->execute([$id]); $atual=$q->fetch(); if(!$atual) throw new Exception('Produto não encontrado.');
                $pdo->prepare('UPDATE produtos SET nome=?,codigo=?,categoria=?,descricao=?,quantidade=?,unidade=?,estoque_minimo=?,preco=? WHERE id=?')->execute([$nome,$codigo?:null,$categoria?:null,$desc,$qtd,$un,$min,$preco,$id]);
                $delta=$qtd-(float)$atual['quantidade'];
                if(abs($delta)>0.00001) $pdo->prepare('INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,?, ?,?)')->execute([$id,$_SESSION['usuario']['id'],$delta>0?'entrada':'saida',abs($delta),'Ajuste no cadastro/edição do produto']);
                $pdo->commit();
            } else {
                $pdo->beginTransaction();
                $pdo->prepare('INSERT INTO produtos(nome,codigo,categoria,descricao,quantidade,unidade,estoque_minimo,preco) VALUES(?,?,?,?,?,?,?,?)')->execute([$nome,$codigo?:null,$categoria?:null,$desc,$qtd,$un,$min,$preco]);
                $novo=(int)$pdo->lastInsertId();
                if($qtd>0) $pdo->prepare("INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,'entrada',?,'Saldo inicial')")->execute([$novo,$_SESSION['usuario']['id'],$qtd]);
                $pdo->commit();
            }
            flash('Produto salvo com sucesso.');
        } elseif ($acao === 'movimentar') {
            $id=(int)($_POST['id']??0); $mov=(float)str_replace(',','.',$_POST['movimento']??'0'); $tipo=$_POST['tipo']??''; $obs=trim($_POST['observacao']??'');
            if($mov<=0 || !in_array($tipo,['entrada','saida'],true)) throw new Exception('Informe uma quantidade e um tipo de movimentação válidos.');
            $pdo->beginTransaction(); $q=$pdo->prepare('SELECT quantidade FROM produtos WHERE id=? AND ativo=1 FOR UPDATE'); $q->execute([$id]); $p=$q->fetch();
            if(!$p) throw new Exception('Produto não encontrado ou inativo.');
            if($tipo==='saida' && (float)$p['quantidade']<$mov) throw new Exception('Estoque insuficiente para essa saída.');
            $delta=$tipo==='entrada'?$mov:-$mov;
            $pdo->prepare('UPDATE produtos SET quantidade=quantidade+? WHERE id=?')->execute([$delta,$id]);
            $pdo->prepare('INSERT INTO estoque_movimentacoes(produto_id,usuario_id,tipo,quantidade,observacao) VALUES(?,?,?,?,?)')->execute([$id,$_SESSION['usuario']['id'],$tipo,$mov,$obs?:'Movimentação manual']);
            $pdo->commit(); flash('Movimentação registrada.');
        } elseif ($acao === 'desativar') {
            $pdo->prepare('UPDATE produtos SET ativo=IF(ativo=1,0,1) WHERE id=?')->execute([(int)$_POST['id']]); flash('Status do produto alterado.');
        }
    } catch(Throwable $e) { if($pdo->inTransaction())$pdo->rollBack(); error_log('Estoque: '.$e->getMessage()); flash($e instanceof PDOException?'Não foi possível salvar. Verifique se o código do produto já está em uso.':$e->getMessage()); }
    header('Location: estoque.php'); exit;
}
$edit=null; if(isset($_GET['editar'])){$s=$pdo->prepare('SELECT * FROM produtos WHERE id=?');$s->execute([(int)$_GET['editar']]);$edit=$s->fetch();}
$prod=$pdo->query('SELECT * FROM produtos ORDER BY nome')->fetchAll();
$movs=$pdo->query('SELECT m.*,p.nome produto,u.nome usuario FROM estoque_movimentacoes m JOIN produtos p ON p.id=m.produto_id JOIN usuarios u ON u.id=m.usuario_id ORDER BY m.criado_em DESC LIMIT 30')->fetchAll();
?>
<!DOCTYPE html><html lang="pt-br"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Estoque</title><link rel="stylesheet" href="style.css"></head><body><main class="app"><header class="topbar"><h2>Controle de estoque</h2><a class="link-sair" href="painel.php">← Painel</a></header><section class="content"><?php mostrar_flash();?>
<h1><?= $edit?'Editar produto/material':'Cadastrar produto ou material' ?></h1>
<form class="form-box" method="post"><input type="hidden" name="acao" value="salvar"><input type="hidden" name="id" value="<?=h($edit['id']??'')?>">
<div class="form-row"><div><label>Nome *</label><input name="nome" required maxlength="150" value="<?=h($edit['nome']??'')?>"></div><div><label>Código</label><input name="codigo" maxlength="60" value="<?=h($edit['codigo']??'')?>"></div><div><label>Categoria</label><input name="categoria" maxlength="80" value="<?=h($edit['categoria']??'')?>" placeholder="Papel, tinta, embalagem..."></div></div>
<label>Descrição</label><textarea name="descricao"><?=h($edit['descricao']??'')?></textarea>
<div class="form-row"><div><label>Quantidade disponível *</label><input type="number" min="0" step="0.01" name="quantidade" required value="<?=h($edit['quantidade']??'0')?>"></div><div><label>Unidade *</label><input name="unidade" required maxlength="20" value="<?=h($edit['unidade']??'un')?>" placeholder="un, resma, kg, litro"></div><div><label>Estoque mínimo *</label><input type="number" min="0" step="0.01" name="estoque_minimo" required value="<?=h($edit['estoque_minimo']??'0')?>"></div></div>
<label>Preço unitário de venda (R$)</label><input type="number" min="0" step="0.01" name="preco" value="<?=h($edit['preco']??'0')?>"><button>Salvar produto/material</button><?php if($edit):?><a href="estoque.php">Cancelar edição</a><?php endif;?></form>
<h2>Produtos e materiais</h2><div class="table-wrap"><table><thead><tr><th>Nome / código</th><th>Categoria</th><th>Disponível</th><th>Mínimo</th><th>Preço</th><th>Status</th><th>Ações</th></tr></thead><tbody><?php foreach($prod as $x):?><tr><td><strong><?=h($x['nome'])?></strong><br><small><?=h($x['codigo']?:'Sem código')?> · <?=h($x['unidade'])?></small></td><td><?=h($x['categoria']?:'—')?></td><td><?=h($x['quantidade'])?> <?php if((float)$x['quantidade']<=(float)$x['estoque_minimo']):?><strong style="color:#b42318"> · Estoque baixo</strong><?php endif;?></td><td><?=h($x['estoque_minimo'])?></td><td>R$ <?=number_format((float)$x['preco'],2,',','.')?></td><td><?=$x['ativo']?'Ativo':'Desativado'?></td><td><a href="?editar=<?=$x['id']?>">Editar</a><form class="inline" method="post"><input type="hidden" name="acao" value="desativar"><input type="hidden" name="id" value="<?=$x['id']?>"><button class="mini"><?=$x['ativo']?'Desativar':'Ativar'?></button></form><?php if($x['ativo']):?><form class="inline" method="post"><input type="hidden" name="acao" value="movimentar"><input type="hidden" name="id" value="<?=$x['id']?>"><input class="qty" name="movimento" type="number" step="0.01" min="0.01" placeholder="Qtd" required><select name="tipo"><option value="entrada">Entrada</option><option value="saida">Saída</option></select><input name="observacao" placeholder="Motivo (opcional)" style="max-width:140px"><button class="mini">Movimentar</button></form><?php endif;?></td></tr><?php endforeach;?><?php if(!$prod):?><tr><td colspan="7">Nenhum produto cadastrado.</td></tr><?php endif;?></tbody></table></div>
<h2>Últimas movimentações</h2><div class="table-wrap"><table><thead><tr><th>Data</th><th>Produto</th><th>Movimento</th><th>Quantidade</th><th>Responsável</th><th>Observação</th></tr></thead><tbody><?php foreach($movs as $m):?><tr><td><?=date('d/m/Y H:i',strtotime($m['criado_em']))?></td><td><?=h($m['produto'])?></td><td><?=h(ucfirst($m['tipo']))?></td><td><?=number_format((float)$m['quantidade'],2,',','.')?></td><td><?=h($m['usuario'])?></td><td><?=h($m['observacao'])?></td></tr><?php endforeach;?><?php if(!$movs):?><tr><td colspan="6">Nenhuma movimentação registrada.</td></tr><?php endif;?></tbody></table></div>
</section></main></body></html>
