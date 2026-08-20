<?php
namespace TAXONOMY_NAMESPACE;

use SkillDo\Cms\Taxonomy\Taxonomy;

/**
 * Taxonomy phải được đăng ký trong hook `init`.
 * File bootstrap đi kèm đã tự gọi:
 *
 * add_action('init', [TAXONOMY_CLASS_NAME::class, 'register']);
 */
class TAXONOMY_CLASS_NAME
{
    static function register(): void
    {
//TAXONOMY_BODY
    }
}
