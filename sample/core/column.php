<?php
namespace COLUMN_NAMESPACE;

use SkillDo\Cms\Table\SKDColumn;

/**
 * Cách dùng trong Table::getColumns()
 *
 * 'my_column' => [
 *     'label'  => trans('table.myColumn'),
 *     'column' => fn ($item, $args) => COLUMN_CLASS_NAME::make('my_column', $item, $args),
 * ],
 */
class COLUMN_CLASS_NAME extends SKDColumn {

    public function view(): string
    {
        return '';
    }
}
