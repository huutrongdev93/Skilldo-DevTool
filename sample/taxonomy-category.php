<?php
/**
 * Category Type
 */
$label = [
    'name' => '',
    'singular' => '',
];

$supports = [
    'group'  => ['info', 'media', 'seo', 'theme'],
    'field'  => [
        'title', 'excerpt', 'content', 'image', 'public', 'slug', 'seo_title', 'seo_keywords', 'seo_description', 'theme_layout', 'theme_view'
    ]
];

$args = [
    'labels' => $label, //Gọi các label trong biến $label ở trên
    'public' => true, //Kích hoạt post type
    'show_in_nav_menus' => true, //hiển thị bên trang quản lý menu.
    'show_in_nav_admin' => true, //Hiển thị trên thanh Admin bar màu đen.
    'parent'            => true,
    'supports'          => $supports,
];

Taxonomy::addCategory('TAXONOMY_CATE_TYPE', 'TAXONOMY_POST_TYPE', $args);