<?php
namespace SkillDo\DevTool\Commands;

class CommandCmsLangBuild extends Command {

    public function paramCheck(): bool
    {
        if(count($this->params) >= 2) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        if($this->paramsCount == 0) {
            response()->success('[[;#00ee11;]success!]');
        }

        if($this->paramsCount == 1) {

            if($this->params[0] == 'js') {

                \Language::buildJs();

                $this->status = 'success';

                $this->message = '[[;#00ee11;]success!]';
            }
        }

        $this->response();
    }
}