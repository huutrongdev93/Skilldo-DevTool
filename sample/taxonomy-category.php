
        /**
         * Category Type: TAXONOMY_CATE_TYPE
         */
        Taxonomy::addCategory('TAXONOMY_CATE_TYPE', 'TAXONOMY_POST_TYPE', [
            'labels' => [
                'name'          => '',
                'singular_name' => '',
            ],
            'public'             => true,
            'show_in_nav_menus'  => true,
            'show_in_nav_admin'  => true,
            'parent'             => true,
            'supports'           => [
                'info'     => ['name', 'excerpt'],
                'media'    => ['image'],
                'seo'      => ['slug', 'seo_title', 'seo_keywords', 'seo_description'],
                'theme'    => ['theme_layout', 'theme_view'],
                'category' => ['parent_id'],
            ],
            'capabilities'       => [
                'edit'   => 'edit_TAXONOMY_CATE_CAP',
                'delete' => 'delete_TAXONOMY_CATE_CAP',
            ],
        ]);
