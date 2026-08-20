<?php
namespace POPOVER_NAMESPACE;

use SkillDo\Cms\Form\PopoverHandle;
use SkillDo\Cms\Support\Image;
use SkillDo\Http\Request;

/**
 * Popover đã được khai báo trong manifest (plugin.json / theme.json):
 *
 * "cms": { "form": { "popover": { "POPOVER_MODULE": "..." } } }
 *
 * Sau khi khai báo, dùng trong form: $form->popoverAdvance('name', ['search' => 'POPOVER_MODULE']);
 */
class POPOVER_CLASS_NAME extends PopoverHandle
{
    public function __construct()
    {
        $this->setModule('POPOVER_MODULE');
    }

    public function search(Request $request): array
    {
        $items = [];

        //TODO: thay bằng model của bạn
        $query = null;

        if(is_null($query))
        {
            return $items;
        }

        $query
            ->select('id', 'name', 'image')
            ->limit($this->limit)
            ->offset($this->page * $this->limit);

        if(!empty($this->keyword))
        {
            $query->where('name', 'like', '%' . $this->keyword . '%');
        }

        foreach ($query->get() as $value)
        {
            $items[] = [
                'id'    => $value->id,
                'name'  => $value->name,
                'image' => Image::medium($value->image)->link(),
            ];
        }

        return $items;
    }

    public function value(Request $request, $listId): array
    {
        $items = [];

        if(noItems($listId))
        {
            return $items;
        }

        //TODO: thay bằng model của bạn
        $objects = [];

        foreach ($objects as $value)
        {
            $items[] = [
                'id'    => $value->id,
                'name'  => $value->name,
                'image' => Image::medium($value->image)->link(),
            ];
        }

        return $items;
    }
}
