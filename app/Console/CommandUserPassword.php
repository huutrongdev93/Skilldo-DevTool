<?php
namespace DevTool\Console;
use SkillDo\Support\Auth;
use DevTool\Commands\Command;
use SkillDo\Cms\Models\User;

class CommandUserPassword extends Command {

    protected string $signature = 'user:password {username} {password}';

    protected string $description = 'Change password a user';

    public function handle(): bool
    {
        $username = $this->argument('username');

        if(!preg_match('/^[a-zA-Z0-9@-]+$/', $username)) {
            $this->line('Error: username không hợp lệ');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username: '.$username);
            return self::ERROR;
        }

        $password = $this->argument('password');

        $userEdit = User::where('username', $username)->first();

        if(!hasItems($userEdit)) {
            $this->line('Error: Không tìm thấy account có username bạn đã nhập');
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username: '.$username);
            return self::ERROR;
        }

        $userCurrent = Auth::user();

        if($userCurrent->id != $userEdit->id && !Auth::hasCap('edit_users')) {
            $this->line(trans('admin::user.ajax.changePassword.role'));
            return self::ERROR;
        }

        $password = apply_filters('pre_update_password', $password, $userEdit);

        $password = Auth::generatePassword($password, $userEdit->salt);

        $error = User::where('id', $userEdit->id)->update(['password' => $password]);

        if(is_skd_error($error))
        {
            $this->line($error->first());
            $this->line('+ '.$this->fullCommand());
            $this->line('+ Username: '.$username);
            return self::ERROR;
        }

        $this->line(trans('DevTool::user.ajax.changePassword.success'));

        return self::SUCCESS;
    }
}