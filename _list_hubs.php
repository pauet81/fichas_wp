<?php
/**
 * List hub pages (pages) with hierarchy and URLs.
 * Run: php /home/pauca/proyectos/fichas/wordpress/_list_hubs.php
 */

require_once __DIR__ . '/wp-load.php';

if (php_sapi_name() !== 'cli') {
    echo "Run from CLI.\n";
    exit(1);
}

$pages = get_pages(array(
    'post_type' => 'page',
    'post_status' => 'publish',
    'sort_column' => 'menu_order,post_title',
    'sort_order' => 'ASC',
));

$by_parent = array();
foreach ($pages as $p) {
    $by_parent[$p->post_parent][] = $p;
}

function print_tree($parent_id, $by_parent, $indent) {
    if (empty($by_parent[$parent_id])) {
        return;
    }
    foreach ($by_parent[$parent_id] as $p) {
        $url = get_permalink($p->ID);
        echo str_repeat('  ', $indent) . '- ' . $p->post_title . ' (' . $url . ')' . "\n";
        print_tree($p->ID, $by_parent, $indent + 1);
    }
}

echo "Hubs (pages) hierarchy:\n";
print_tree(0, $by_parent, 0);
