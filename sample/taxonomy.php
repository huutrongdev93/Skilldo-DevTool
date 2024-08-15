<?php
/**
 * Post Type
 */
$label = [
    'name' => '', //Tên post type dạng số nhiều
    'singular_name' => '' //Tên post type dạng số ít
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
    'menu_position'     => 5,    //Thứ tự vị trí hiển thị trong menu (tay trái)
    'menu_icon'         => '',   //Đường dẫn tới icon sẽ hiển thị
    TAXONOMY_CATE_TYPE
    'supports'          => $supports,
];

Taxonomy::addPost('TAXONOMY_POST_TYPE', $args);