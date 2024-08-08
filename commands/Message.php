<?php
namespace SkillDo\DevTool\Commands;

use JetBrains\PhpStorm\NoReturn;

class Message
{
    private array $message;

    public function __construct()
    {
        $this->message = [];
    }

    public function line($message, $color = null, $isBold = false): static
    {
        if(is_string($message) || is_numeric($message)) {

            if($color == 'green') {
                return $this->green($message, $isBold);
            }

            if($color == 'blue') {
                return $this->blue($message, $isBold);
            }

            if($color == 'yellow') {
                return $this->yellow($message, $isBold);
            }

            $this->message[] = $message;
        }

        if($message instanceof \Closure) {

            $consoleMessage = new Message();

            $message($consoleMessage);

            $this->message[] = $consoleMessage->getLine();
        }

        return $this;
    }

    public function blue(string $message, bool $isBold = false): static
    {
        $message = '[['.(($isBold) ? '!b' : '').';blue;]'.$message.']';

        $this->message[] = $message;

        return $this;
    }

    public function green(string $message, bool $isBold = false): static
    {
        $message = '[['.(($isBold) ? '!b' : '').';#00ee11;]'.$message.']';

        $this->message[] = $message;

        return $this;
    }

    public function yellow(string $message, bool $isBold = false): static
    {
        $message = '[['.(($isBold) ? '!b' : '').';#ff9b00;]'.$message.']';

        $this->message[] = $message;

        return $this;
    }

    public function getLine(): string
    {
        $message = '';

        if(have_posts($this->message)) {
            $message = implode(" ", $this->message);
        }

        return $message;
    }

    public function getMessageSend($messageMain): array
    {
        $messages = $this->message;

        if(have_posts($messages)) {

            $messageMain = array_shift($messages);
        }

        return [$messageMain, (have_posts($messages)) ? array_values($messages) : []];
    }

    public function data(array $data): bool
    {
        if(!have_posts($this->message)) {
            return false;
        }

        [$messageMain, $message] = $this->getMessageSend('');

        $this->message = [];
        $this->message[] = $messageMain;
        $this->message[] = $data;

        return true;
    }

    #[NoReturn]
    public function success(): void
    {
        [$messageMain, $message] = $this->getMessageSend('Success!');

        response()->success($messageMain, (!empty($message[0])&& is_array($message[0])) ? $message[0] : $message);
    }

    #[NoReturn]
    public function error(): void
    {
        [$messageMain, $message] = $this->getMessageSend('Error!');

        response()->error($messageMain, (!empty($message[0])&& is_array($message[0])) ? $message[0] : $message);
    }
}