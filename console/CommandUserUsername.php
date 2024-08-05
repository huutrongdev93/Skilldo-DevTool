<?php
namespace SkillDo\DevTool\Console;
use SkillDo\DevTool\Commands\Command;
use User;

class CommandUserUsername extends Command {

    protected string $signature = 'user::username {usernameOld} {usernameNew}';

    protected string $description = 'Change username a user';

    public function handle(): bool
    {
        $usernameOld = $this->argument('usernameOld');

        if(!preg_match('/^[a-zA-Z0-9@-]+$/', $usernameOld)) {
            $this->line('Error: username không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username old: '.$usernameOld);
            return self::ERROR;
        }

        $usernameNew = $this->argument('usernameNew');

        if(!preg_match('/^[a-zA-Z0-9@-]+$/', $usernameNew)) {
            $this->line('Error: username mới không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username new: '.$usernameNew);
            return self::ERROR;
        }

        $userEdit = User::where('username', $usernameOld)->first();

        if(!have_posts($userEdit)) {
            $this->line('Error: Không tìm thấy account có username bạn đã nhập');
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        if(User::usernameExists($usernameNew)) {
            $this->line('Error: username mới đã được sử dụng');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username new: '.$usernameNew);
            return self::ERROR;
        }

        $error = User::where('id', $userEdit->id)->update(['username' => $usernameNew]);

        if(is_skd_error($error)) {
            $this->line($error->first());
            $this->line('+ '.$this->fullCommand());
            return self::ERROR;
        }

        $this->line('Thay đổi username user thành công');

        return self::SUCCESS;
    }
}