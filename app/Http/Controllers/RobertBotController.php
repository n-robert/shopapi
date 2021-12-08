<?php

namespace App\Http\Controllers;

use App\Services\FootBot;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;

class RobertBotController extends Controller
{
    use FootBot;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var Api
     */
    protected $telegram;

    /**
     * @var User
     */
    protected $bot;

    /**
     * @var int
     */
    protected $chatId;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var Update
     */
    protected $update;

    /**
     * @var string
     */
    protected $callbackId;

    /**
     * @var User
     */
    protected $user;

    /**
     * @var string
     */
    protected $userName;

    /**
     * @var ChatMember
     */
    protected $chatMember;

    /**
     * @var bool
     */
    protected $isAdmin;

    /**
     * @var string
     */
    protected $text;

    /**
     * @param string|null $token
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct(string $token = null)
    {
        $this->token = $token ?: env('TELEGRAM_ROBERTBOT_TOKEN');
        $this->telegram = new Api($this->token);
        $this->bot = $this->telegram->getMe();
    }

    /**
     * @return false|\Illuminate\Http\RedirectResponse
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function setWebHook()
    {
        $response = $this->telegram->setWebhook([
            'url' => Url::secure($this->token . '/webhook'),
            'drop_pending_updates' => true,
        ]);

        return $response === true ? redirect()->back() : false;
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function handleUpdate()
    {
        $this->setWebHook();

        $this->update = $this->telegram->getWebhookUpdate();

        if (isset($this->update['callback_query'])) {
            $this->callbackId = $this->update['callback_query']['id'];
            $this->message = $this->update['callback_query']['message'];
            $fromUser = $this->update['callback_query']['from'];
            $method = $this->update['callback_query']['data'];
        } else {
            $this->message = $this->update['message'];
            $fromUser = $this->message['from'];
            $toHear = array_merge(static::$adminCommands1, static::$adminCommands2);

            do {
                if (Str::startsWith($this->message['text'], '/')) {
                    $method = $this->message['text'];
                    break;
                }

                foreach (static::$toHearByName as $command => $text) {
                    if ($this->hearsByName($text)) {
                        $method = $command;
                        break(2);
                    }
                }

                foreach ($toHear as $command => $text) {
                    if ($this->hears($text)) {
                        $method = $command;
                        break(2);
                    }
                }

                foreach (static::$toBeSaid as $command => $text) {
                    if ($this->said($text)) {
                        $method = $command;
                        break(2);
                    }
                }

                $adminCommands = array_merge(static::$adminCommands1, static::$adminCommands2);
                $userCommands = array_merge(static::$myCommands, static::$guestCommands);

                if ($method = array_search($this->message['text'], array_merge($adminCommands, $userCommands))) {
                    break;
                }
            } while (false);
        }

        if (!$method) {
            return;
        }

        $this->chatId = $this->message['chat']['id'];
        $this->chatMember =
            $this
                ->telegram
                ->getChatMember([
                    'chat_id' => $this->chatId,
                    'user_id' => $fromUser['id'],
                ]);
        $this->user = $this->chatMember->user;
        $this->isAdmin =
            in_array($this->chatMember->status, ['creator', 'administrator']) ||
            in_array($this->user->username, static::$admins);
        $this->keyboard = $this->getKeyboard();
        $this->adminKeyboard = $this->getKeyboard('admin');
        $this->userName = '@' . ($this->user->username ?: hash('crc32b', $this->user->id)) . ' ';
        $this->userName .= $this->user->firstName;

        $method = str_replace(['@' . $this->bot->username, '/'], '', $method);
        $agrs = [];

        switch ($method) {
            case 'run':
                if ($this->isAdmin) {
                    $method = 'newSchedule';
                } else {
                    $method = 'deleteMessage';
                }

                break;
            case 'stop':
                if ($this->isAdmin) {
                    $method = 'closeOldGame';
                    $agrs = [true];
                } else {
                    $method = 'deleteMessage';
                }

                break;
            case 'joke':
                $method = 'toJoke';

                break;
            case 'shout':
                $method = 'toShout';

                break;
        }

        if (method_exists($this, $method)) {
            call_user_func_array([$this, $method], $agrs);
        }
    }

    /**
     * @param string $pattern
     * @return false|int
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function hearsByName(string $pattern)
    {
        $botName = $this->bot->firstName;

        return $this->hears("^($botName|бот)(\s|,\s)$pattern$");
    }

    /**
     * @param string $pattern
     * @param Message|null $message
     * @return false|int
     */
    protected function hears(string $pattern, Message $message = null)
    {
        $message = $message ?: $this->message;

        return preg_match("/$pattern/iu", $message['text']);
    }

    /**
     * @param string $pattern
     * @param Message|null $message
     * @return false|int
     */
    protected function said(string $pattern, Message $message = null)
    {
        $message = $message ?: $this->message;

        return preg_match("/$pattern/iu", $message['reply_to_message']['text']);
    }

    public function test()
    {}
}
