<?php
declare(strict_types=1);

const DATA_DIR = __DIR__ . '/../data';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function dataset_file(string $type): string
{
    $allowed = ['noticias', 'lancamentos', 'jogos'];

    if (!in_array($type, $allowed, true)) {
        $type = 'noticias';
    }

    return DATA_DIR . '/' . $type . '.json';
}

function load_items(string $type): array
{
    $file = dataset_file($type);

    if (!file_exists($file)) {
        return [];
    }

    $json = file_get_contents($file);
    $items = json_decode($json ?: '[]', true);

    return is_array($items) ? $items : [];
}

function save_items(string $type, array $items): void
{
    if (!is_dir(DATA_DIR)) {
        mkdir(DATA_DIR, 0777, true);
    }

    file_put_contents(
        dataset_file($type),
        json_encode(array_values($items), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function sort_by_date_desc(array $items): array
{
    usort($items, fn ($a, $b) => strcmp((string) ($b['data'] ?? ''), (string) ($a['data'] ?? '')));

    return $items;
}

function page_name(): string
{
    return basename((string) parse_url($_SERVER['REQUEST_URI'] ?? 'index.php', PHP_URL_QUERY)) ?: 'index.php';
}
?>
