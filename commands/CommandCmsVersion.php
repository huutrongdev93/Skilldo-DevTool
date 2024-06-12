<?php
namespace SkillDo\DevTool\Commands;

use SKDService;

class CommandCmsVersion extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) > 1) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if($this->paramsCount == 0) {

            $this->status = 'success';

            $this->message = \Cms::version();

            $this->response();
        }

        if($this->params[0] == 'last') {

            \Language::buildJs();

            $this->status = 'success';

            $this->message = SKDService::cms()->version();

            $this->response();
        }

        $this->response();
    }
}