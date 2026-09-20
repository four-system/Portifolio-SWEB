<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    return $pdo;
}

function entity_config(string $type): array
{
    $entities = [
        'noticias' => [
            'table' => 'noticias',
            'fields' => ['titulo', 'categoria', 'data', 'resumo', 'fonte', 'link'],
            'order' => 'data DESC, id DESC',
        ],
        'lancamentos' => [
            'table' => 'lancamentos',
            'fields' => ['titulo', 'categoria', 'data', 'plataformas', 'resumo', 'fonte', 'link'],
            'order' => 'data ASC, id ASC',
        ],
        'jogos' => [
            'table' => 'jogos',
            'fields' => ['titulo', 'categoria', 'nota', 'resumo'],
            'order' => 'id DESC',
        ],
    ];

    return $entities[$type] ?? $entities['noticias'];
}

function load_items(string $type): array
{
    $config = entity_config($type);
    $sql = 'SELECT * FROM ' . $config['table'] . ' ORDER BY ' . $config['order'];

    return db()->query($sql)->fetchAll();
}

function find_item(string $type, int $id): ?array
{
    $config = entity_config($type);
    $stmt = db()->prepare('SELECT * FROM ' . $config['table'] . ' WHERE id = :id');
    $stmt->execute(['id' => $id]);
    $item = $stmt->fetch();

    return $item ?: null;
}

function create_item(string $type, array $record): void
{
    $config = entity_config($type);
    $fields = $config['fields'];
    $columns = implode(', ', $fields);
    $placeholders = ':' . implode(', :', $fields);
    $stmt = db()->prepare('INSERT INTO ' . $config['table'] . " ($columns) VALUES ($placeholders)");
    $stmt->execute(filter_record($record, $fields));
}

function update_item(string $type, int $id, array $record): void
{
    $config = entity_config($type);
    $fields = $config['fields'];
    $set = implode(', ', array_map(fn ($field) => "$field = :$field", $fields));
    $values = filter_record($record, $fields);
    $values['id'] = $id;
    $stmt = db()->prepare('UPDATE ' . $config['table'] . " SET $set WHERE id = :id");
    $stmt->execute($values);
}

function delete_item(string $type, int $id): void
{
    $config = entity_config($type);
    $stmt = db()->prepare('DELETE FROM ' . $config['table'] . ' WHERE id = :id');
    $stmt->execute(['id' => $id]);
}

function filter_record(array $record, array $fields): array
{
    $filtered = [];

    foreach ($fields as $field) {
        $filtered[$field] = trim((string) ($record[$field] ?? ''));
    }

    return $filtered;
}

function sort_by_date_desc(array $items): array
{
    usort($items, fn ($a, $b) => strcmp((string) ($b['data'] ?? ''), (string) ($a['data'] ?? '')));

    return $items;
}

function page_name(): string
{
    return basename((string) parse_url($_SERVER['REQUEST_URI'] ?? 'index.php', PHP_URL_PATH)) ?: 'index.php';
}
?>
