<?php

namespace App\Http\Controllers;

use App\Services\TheMealDb;
use Illuminate\Http\Request;
use App\Models\TelegramSouschef;
use App\Services\JokeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\ChatMember;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;

class SouschefController extends Controller
{
    use TheMealDb;

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
     * @var string
     */
    protected $endpoint;

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
     * @var string
     */
    protected $callbackData;

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
     * @var string[]
     */
    static $toHear = [
        'categories' => 'Categories',
        'mealbyletter' => 'Meal by first letter',
        'mealbyname' => 'Meal by name',
        'mealbyingr' => 'Meal by ingredients',
    ];

    /**
     * @var string[]
     */
    static $toBeSaid = [
        'mealbyingr' => 'Ingredients',
    ];

    /**
     * @param string|null $token
     * @param string $endpoint
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct(string $token = null, string $endpoint = 'themealdb')
    {
        $this->token = $token ?: env('TELEGRAM_SOUSCHEFBOT_TOKEN');
        $this->telegram = new Api($this->token);
        $this->bot = $this->telegram->getMe();
        $this->endpoint = env(strtoupper($endpoint) . '_ENDPOINT');
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
        $method = null;

        if (isset($this->update['callback_query'])) {
            $this->callbackId = $this->update['callback_query']['id'];
            $this->message = $this->update['callback_query']['message'];
            $fromUser = $this->update['callback_query']['from'];
            $this->callbackData = $this->update['callback_query']['data'];
            $method = explode('|', $this->callbackData);
            $method = trim($method[0]);
        } else {
            $this->message = $this->update['message'];
            $fromUser = $this->message['from'];

            do {
                if (Str::startsWith($this->message['text'], '/')) {
                    $method = $this->message['text'];
                    break;
                }

                foreach (static::$toHear as $command => $text) {
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
        $this->isAdmin = in_array($this->chatMember->status, ['creator', 'administrator']);

        $method = str_replace(['@' . $this->bot->username, '/'], '', $method);
        $method = static::$methodAlias[$method]['method'] ?: $method;
        $agrs = static::$methodAlias[$method]['args'] ?? [];

        if (method_exists($this, $method)) {
            $this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
            call_user_func_array([$this, $method], $agrs);
        }
    }



    /**
     * @param int $messageId
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function pinChatMessage(int $messageId)
    {
        $this->telegram->pinChatMessage(
            [
                'chat_id' => $this->chatId,
                'message_id' => $messageId,
            ]
        );
    }

    /**
     * @param bool $deleteOriginal
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function deleteMessage(bool $deleteOriginal = true)
    {
        $this->telegram->deleteMessage([
            'chat_id' => $this->chatId,
            'message_id' => $this->message['message_id'],
        ]);

        if ($deleteOriginal) {
            $this->telegram->deleteMessage([
                'chat_id' => $this->chatId,
                'message_id' => $this->message['reply_to_message']['message_id'],
            ]);
        }
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
    {
        $response = json_decode(Http::get('https://api.spoonacular.com/recipes/findByIngredients', [
                'apiKey' => env('SPOONACULAR_API_KEY'),
                'ingredients' => 'chicken,mushroom',
                'offset' => 0,
                'number' => 3,
            ]
        ));
        echo '<pre>', print_r($response), '</pre>';
    }
}
