        /**
         * Post Type: TAXONOMY_POST_TYPE
         */
        Taxonomy::addPost('TAXONOMY_POST_TYPE', [
            'labels' => [
                'name'          => '',
                'singular_name' => '',
            ],
            'public'             => true,
            'show_in_nav_menus'  => true,
            'show_in_nav_admin'  => true,
            'menu_position'      => 5,
            'menu_icon'          => '',
            'supports'           => [
                'info'  => ['title', 'excerpt', 'content', 'public'],
                'media' => ['image'],
                'seo'   => ['slug', 'seo_title', 'seo_keywords', 'seo_description'],
                'theme' => ['theme_layout', 'theme_view'],
            ],
            'capabilities'       => [
                'view'   => 'view_TAXONOMY_POST_CAP',
                'add'    => 'add_TAXONOMY_POST_CAP',
                'edit'   => 'edit_TAXONOMY_POST_CAP',
                'delete' => 'delete_TAXONOMY_POST_CAP',
            ],
        ]);
