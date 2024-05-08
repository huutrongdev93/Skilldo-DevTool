<?php
namespace SkillDo\DevTool\Commands;

class CommandCacheClear extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 0) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        \CacheHandler::flush();

        $this->status = 'success';

        $this->message = '[[;#00ee11;]success!]';

        $this->response();
    }
}