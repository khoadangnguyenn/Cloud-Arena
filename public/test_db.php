<?php
require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/core/Database.php';
$db = new Database();
$db->query('SELECT COUNT(*) as count FROM news');
$row = $db->single();
echo "Total news: " . $row->count . "\n";
$db->query('SELECT COUNT(*) as count FROM news WHERE status = "published"');
$row = $db->single();
echo "Published news: " . $row->count . "\n";
$db->query('SELECT id, title, status, publish_at FROM news ORDER BY id DESC LIMIT 10');
$rows = $db->resultSet();
foreach($rows as $r) {
    echo "ID: {$r->id} | Status: {$r->status} | PublishAt: " . ($r->publish_at ?? 'NULL') . " | Title: {$r->title}\n";
}
