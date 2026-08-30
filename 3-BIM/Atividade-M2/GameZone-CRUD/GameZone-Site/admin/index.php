<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/data.php';

$entities = [
    'noticias' => [
        'label' => 'Notícias',
        'singular' => 'notícia',
        'fields' => [
            'titulo' => ['label' => 'Título', 'type' => 'text', 'required' => true],
            'categoria' => ['label' => 'Categoria', 'type' => 'text', 'required' => true],
            'data' => ['label' => 'Data', 'type' => 'date', 'required' => true],
            'resumo' => ['label' => 'Resumo', 'type' => 'textarea', 'required' => true],
            'fonte' => ['label' => 'Fonte', 'type' => 'text', 'required' => true],
            'link' => ['label' => 'Link da fonte', 'type' => 'url', 'required' => true],
        ],
    ],
    'lancamentos' => [
        'label' => 'Lançamentos',
        'singular' => 'lançamento',
        'fields' => [
            'titulo' => ['label' => 'Título', 'type' => 'text', 'required' => true],
            'categoria' => ['label' => 'Categoria', 'type' => 'text', 'required' => true],
            'data' => ['label' => 'Data de lançamento', 'type' => 'date', 'required' => true],
            'plataformas' => ['label' => 'Plataformas', 'type' => 'text', 'required' => true],
            'resumo' => ['label' => 'Resumo', 'type' => 'textarea', 'required' => true],
            'fonte' => ['label' => 'Fonte', 'type' => 'text', 'required' => true],
            'link' => ['label' => 'Link da fonte', 'type' => 'url', 'required' => true],
        ],
    ],
    'jogos' => [
        'label' => 'Jogos',
        'singular' => 'jogo',
        'fields' => [
            'titulo' => ['label' => 'Título', 'type' => 'text', 'required' => true],
            'categoria' => ['label' => 'Categoria', 'type' => 'select', 'required' => true, 'options' => ['fps' => 'FPS', 'rpg' => 'RPG', 'esports' => 'E-sports']],
            'nota' => ['label' => 'Nota', 'type' => 'number', 'required' => true],
            'resumo' => ['label' => 'Resumo', 'type' => 'textarea', 'required' => true],
        ],
    ],
];

$type = $_GET['tipo'] ?? 'noticias';
$type = array_key_exists($type, $entities) ? $type : 'noticias';
$action = $_GET['acao'] ?? 'listar';
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['tipo'] ?? $type;
    $type = array_key_exists($type, $entities) ? $type : 'noticias';
    $postedId = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $items = load_items($type);
    $record = [];

    foreach ($entities[$type]['fields'] as $field => $config) {
        $record[$field] = trim((string) ($_POST[$field] ?? ''));
    }

    if ($postedId > 0) {
        foreach ($items as $index => $item) {
            if ((int) $item['id'] === $postedId) {
                $record['id'] = $postedId;
                $items[$index] = $record;
                break;
            }
        }
        $message = ucfirst($entities[$type]['singular']) . ' atualizado com sucesso.';
    } else {
        $record['id'] = next_id($items);
        $items[] = $record;
        $message = ucfirst($entities[$type]['singular']) . ' cadastrado com sucesso.';
    }

    save_items($type, $items);
    $action = 'listar';
}

if ($action === 'excluir' && $id > 0) {
    $items = array_filter(load_items($type), fn ($item) => (int) $item['id'] !== $id);
    save_items($type, $items);
    $message = ucfirst($entities[$type]['singular']) . ' excluído com sucesso.';
    $action = 'listar';
}

$items = load_items($type);
$editing = $action === 'editar' && $id > 0 ? find_item($type, $id) : null;
$showForm = $action === 'novo' || $editing !== null;
$pageTitle = $showForm ? ($editing ? 'Editar ' : 'Cadastrar ') . $entities[$type]['singular'] : 'Gerenciar ' . $entities[$type]['label'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin | GameZone</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../styles.css">
</head>
<body>
  <header class="site-header" id="topo">
    <nav class="navbar admin-navbar" aria-label="Navegação administrativa">
      <a class="brand" href="../index.php" aria-label="GameZone - site público">
        <img src="../assets/gamezone-logo.svg" alt="" class="brand-logo">
        <span>GameZone Admin</span>
      </a>
      <a class="btn btn-secondary admin-public-link" href="../index.php">Ver site</a>
    </nav>
  </header>

  <main>
    <section class="section page-hero admin-hero">
      <p class="eyebrow">Área administrativa</p>
      <h1>CRUD GameZone</h1>
      <p>Cadastre, visualize, edite e exclua conteúdos que aparecem nas páginas públicas do site.</p>
    </section>

    <section class="section admin-panel">
      <div class="admin-tabs" aria-label="CRUDs disponíveis">
        <?php foreach ($entities as $key => $entity): ?>
          <a class="<?= $type === $key ? 'active' : '' ?>" href="?tipo=<?= e($key) ?>"><?= e($entity['label']) ?></a>
        <?php endforeach; ?>
      </div>

      <?php if ($message): ?>
        <p class="admin-message"><?= e($message) ?></p>
      <?php endif; ?>

      <div class="admin-toolbar">
        <div>
          <p class="eyebrow">CRUD completo</p>
          <h2><?= e($pageTitle) ?></h2>
        </div>
        <a class="btn btn-primary" href="?tipo=<?= e($type) ?>&acao=novo">Novo cadastro</a>
      </div>

      <?php if ($showForm): ?>
        <form class="admin-form" method="post">
          <input type="hidden" name="tipo" value="<?= e($type) ?>">
          <input type="hidden" name="item_id" value="<?= e((string) ($editing['id'] ?? 0)) ?>">

          <?php foreach ($entities[$type]['fields'] as $field => $config): ?>
            <label for="<?= e($field) ?>"><?= e($config['label']) ?></label>

            <?php if ($config['type'] === 'textarea'): ?>
              <textarea id="<?= e($field) ?>" name="<?= e($field) ?>" rows="4" <?= $config['required'] ? 'required' : '' ?>><?= e((string) ($editing[$field] ?? '')) ?></textarea>
            <?php elseif ($config['type'] === 'select'): ?>
              <select id="<?= e($field) ?>" name="<?= e($field) ?>" <?= $config['required'] ? 'required' : '' ?>>
                <?php foreach ($config['options'] as $value => $label): ?>
                  <option value="<?= e($value) ?>" <?= (($editing[$field] ?? '') === $value) ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            <?php else: ?>
              <input id="<?= e($field) ?>" name="<?= e($field) ?>" type="<?= e($config['type']) ?>" value="<?= e((string) ($editing[$field] ?? '')) ?>" <?= $field === 'nota' ? 'step="0.1" min="0" max="10"' : '' ?> <?= $config['required'] ? 'required' : '' ?>>
            <?php endif; ?>
          <?php endforeach; ?>

          <div class="admin-actions">
            <button class="btn btn-primary" type="submit">Salvar</button>
            <a class="btn btn-secondary" href="?tipo=<?= e($type) ?>">Cancelar</a>
          </div>
        </form>
      <?php endif; ?>

      <div class="admin-table-wrap">
        <table class="admin-table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Título</th>
              <th>Categoria</th>
              <th>Resumo</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td><?= e((string) $item['id']) ?></td>
                <td><?= e($item['titulo']) ?></td>
                <td><?= e($item['categoria']) ?></td>
                <td><?= e($item['resumo']) ?></td>
                <td class="row-actions">
                  <a href="?tipo=<?= e($type) ?>&acao=editar&id=<?= e((string) $item['id']) ?>">Editar</a>
                  <a class="danger-link" href="?tipo=<?= e($type) ?>&acao=excluir&id=<?= e((string) $item['id']) ?>" onclick="return confirm('Deseja excluir este item?')">Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</body>
</html>
