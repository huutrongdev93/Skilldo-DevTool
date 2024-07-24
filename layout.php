<?php
class DevToolLayout {

    static function content(): void
    {
        $layout_list  = Theme_Layout::list();
        self::layoutHome($layout_list);
        self::layoutPage($layout_list);
        self::layoutPostIndex($layout_list);
        self::layoutPostDetail($layout_list);
        if(Plugin::isActive('sicommerce')) {
            self::layoutProductIndex($layout_list);
            self::layoutProductDetail($layout_list);
        }
    }

    static function layoutHome($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout-home', [
            'layout' => Theme_Layout::layout('layout_home')
        ]);
    }

    static function layoutPage($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout', [
            'layout'        => Theme_Layout::layout('layout_page'),
            'layoutList'    => $layout_list,
            'layoutType'    => 'page',
            'heading'       => 'Trang nội dung'
        ]);
    }

    static function layoutPostIndex($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout', [
            'layout'        => Theme_Layout::layout('layout_post_category'),
            'layoutList'    => $layout_list,
            'layoutType'    => 'post-category',
            'heading'       => 'Danh sách bài viết'
        ]);
    }

    static function layoutPostDetail($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout', [
            'layout'        => Theme_Layout::layout('layout_post'),
            'layoutList'    => $layout_list,
            'layoutType'    => 'post',
            'heading'       => 'Chi tiết bài viết'
        ]);
    }

    static function layoutProductIndex($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout', [
            'layout'        => Theme_Layout::layout('layout_products_category'),
            'layoutList'    => $layout_list,
            'layoutType'    => 'products-category',
            'heading'       => 'Danh sách sản phẩm'
        ]);
    }

    static function layoutProductDetail($layout_list): void
    {
        Theme::view('admin/theme-layout/html/dev-tools-layout/layout', [
            'layout'        => Theme_Layout::layout('layout_products'),
            'layoutList'    => $layout_list,
            'layoutType'    => 'products',
            'heading'       => 'Chi tiết sản phẩm'
        ]);
    }

    static function save($result) {

        if($result['status'] == 'error') return $result;

        $layout 				= Request::post('layout');

        $layout_list = Theme_Layout::list();

        if(!empty($layout['home-layout'])) {

            $layout_home = Str::clear($layout['home-layout']);

            Option::update('layout_home', $layout_home );
        }

        if(!empty($layout['page-layout'])) {

            $layout_page  = Str::clear($layout['page-layout']);

            if(isset($layout_list[$layout_page])) {
                Option::update('layout_page', $layout_page );
            }
        }

        if(!empty($layout['post-layout'])) {

            $layout_post  = Str::clear($layout['post-layout']);

            if(isset($layout_list[$layout_post])) {
                Option::update('layout_post', $layout_post );
            }
        }

        if(!empty($layout['post-category-layout'])) {

            $layout_post_category  = Str::clear($layout['post-category-layout']);

            if(isset($layout_list[$layout_post_category])) {
                Option::update('layout_post_category', $layout_post_category );
            }
        }

        if(isset($layout['products-category-layout'])) {

            $layout_products_category   = Str::clear($layout['products-category-layout']);

            if(isset($layout_list[$layout_products_category])) 	Option::update('layout_products_category', $layout_products_category );
        }

        if(isset($layout['products-layout'])) {

            $layout_products   = Str::clear($layout['products-layout']);

            Option::update('layout_products', $layout_products );
        }

        $result['message'] 	= 'Cập nhật dữ liệu thành công';

        $result['status'] 	= 'success';

        return $result;
    }
}