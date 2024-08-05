<?php
use SkillDo\Form\PopoverHandle;
use SkillDo\Http\Request;
class FormPopoverClassName extends PopoverHandle
{
    public function __construct()
    {
        $this->setModule('module_name');
    }

    public function search(Request $request): array
    {
        $items = [];

        $args = Qr::set()
            ->select('id', 'name', 'image')
            ->limit($this->limit)
            ->offset($this->page* $this->limit);

        if(!empty($this->keyword)) {

            $args
                ->where('name', 'like', '%' . $this->keyword . '%');
        }

        $objects = MODULE::gets($args);

        if(have_posts($objects)) {
            foreach ($objects as $value) {
                $items[]  = [
                    'id'        => $value->id,
                    'name'      => $value->name,
                    'image'     => Template::imgLink($value->image),
                ];
            }
        }

        return $items;
    }

    public function value(Request $request, $listId): array
    {
        $items = [];

        if(have_posts($listId)) {

            $objects = MODULE::whereIn('id', $listId)
                ->select('id', 'name', 'image')
                ->fetch();

            foreach ($objects as $value) {

                $items[]  = [
                    'id'        => $value->id,
                    'name'      => $value->name,
                    'image'     => Template::imgLink($value->image),
                ];
            }
        }

        return $items;
    }
}