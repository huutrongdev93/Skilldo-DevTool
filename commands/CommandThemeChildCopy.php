<?php
namespace SkillDo\DevTool\Commands;

class CommandThemeChildCopy extends Command {

    public function paramCheck(): bool
    {
        if($this->paramsCount != 1) {
            return false;
        }
        return true;
    }

    public function run(): void
    {
        $themeName = \Theme::name();

        $pathTheme = 'views/'.$themeName.'/';

        $pathSource = $this->params[0];

        if(!file_exists($pathTheme.$pathSource)) {
            response()->error('file or dir '.$pathTheme.$pathSource.' not exists');
        }

        if(file_exists($pathTheme.'theme-child/'.$pathSource)) {
            response()->error('file or dir '.$pathSource.' is existing in theme-child');
        }

        $isFolder =  true;

        if(is_file($pathTheme.$pathSource)) {
            $isFolder = false;
        }

        $storage = \Storage::disk('views');

        if(!$isFolder) {

            $storage->copy($themeName.'/'.$pathSource, $themeName.'/'.'theme-child/'.$pathSource);

            response()->success('[[b;#00ee11;]copy file success!]', [
                '+ from '.$pathTheme.$pathSource,
                '+ to '.$pathTheme.'theme-child/'.$pathSource,
            ]);
        }

        $allFiles = $storage->allFiles($themeName.'/'.$pathSource);

        foreach ($allFiles as $pathFrom) {

            $pathTo = str_replace($themeName.'/', $themeName.'/theme-child/', $pathFrom);

            $storage->copy($pathFrom, $pathTo);
        }

        response()->success('[[b;#00ee11;]copy folder success!]', [
            '+ from '.$pathTheme.$pathSource,
            '+ to '.$pathTheme.'theme-child/'.$pathSource,
        ]);
    }
}