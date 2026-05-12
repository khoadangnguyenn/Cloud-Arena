<?php
/**
 * Served as /sitemap.xml — lists public URLs; news/products from DB when available.
 */
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';

header('Content-Type: application/xml; charset=UTF-8');

$base = rtrim((string) URLROOT, '/');
$esc = static function ($u) {
    return htmlspecialchars((string) $u, ENT_XML1 | ENT_QUOTES, 'UTF-8');
};

$items = [];

$add = static function ($loc, $lastmod = null) use (&$items, $base) {
    if (strpos($loc, 'http://') !== 0 && strpos($loc, 'https://') !== 0) {
        $loc = $base . '/' . ltrim($loc, '/');
    }
    $items[] = ['loc' => $loc, 'lastmod' => $lastmod];
};

$add('/', null);
foreach (['pages/contact', 'pages/faq', 'products', 'news'] as $p) {
    $add($p, null);
}

try {
    $db = new Database();
    $db->query('SELECT id, created_at FROM products WHERE status = :st ORDER BY id ASC');
    $db->bind(':st', 'active');
    foreach ($db->resultSet() as $row) {
        $id = (int) ($row->id ?? 0);
        if ($id <= 0) {
            continue;
        }
        $lm = !empty($row->created_at) ? date('c', strtotime((string) $row->created_at)) : null;
        $add('products/show/' . $id, $lm);
    }

    $db->query('SELECT id, created_at FROM news ORDER BY id ASC');
    foreach ($db->resultSet() as $row) {
        $id = (int) ($row->id ?? 0);
        if ($id <= 0) {
            continue;
        }
        $lm = !empty($row->created_at) ? date('c', strtotime((string) $row->created_at)) : null;
        $add('news/show/' . $id, $lm);
    }
} catch (Throwable $e) {
    // Static routes only
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($items as $it) {
    echo '  <url>' . "\n";
    echo '    <loc>' . $esc($it['loc']) . '</loc>' . "\n";
    if (!empty($it['lastmod'])) {
        echo '    <lastmod>' . $esc($it['lastmod']) . '</lastmod>' . "\n";
    }
    echo '  </url>' . "\n";
}
echo '</urlset>';
