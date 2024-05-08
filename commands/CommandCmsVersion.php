<?php
namespace SkillDo\DevTool\Commands;

class CommandCmsVersion extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 0) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        $this->status = 'success';

        $this->message = \Cms::version();

        $this->response();
    }
}