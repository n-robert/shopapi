<?php

namespace App\Services;

use App\Models\TelegramFootbot;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Telegram\Bot\Objects\Message;

trait FootBot
{
    /**
     * @var string[]
     */
    static $myCommands = [
        '/addMe' => 'Я +',
        '/addMeMaybe' => 'Я +/-',
        '/removeMe' => 'Я -',
    ];

    /**
     * @var string[]
     */
    static $guestCommands = [
        '/addMyGuest' => 'Друг +',
        '/removeMyGuest' => 'Друг -',
    ];

    /**
     * @var string[]
     */
    static $adminCommands1 = [
        '/setAddress' => 'Адрес',
        '/setDate' => 'Дата',
        '/setTime' => 'Начало игры',
    ];

    /**
     * @var string[]
     */
    static $adminCommands2 = [
        '/setLimit' => 'Лимит игроков',
        '/setPrice' => 'Арендная плата',
    ];

    /**
     * @var string[]
     */
    static $toHearByName = [
        '/run' => '(Запусти )*(новая игра|новую игру)',
        '/joke' => '(Расскажи )*(нам )*(еще )*анекдот',
        '/shout' => '(Ты )*(еще )*(жив(\?)*|здесь(\?)*|(не )*сдох(\?)*)|(.*)*отстой',
    ];

    /**
     * @var string[][]
     */
    static $toShout = [
        'random' => [
            'Ау!',
            'Я! Что случилось?',
            'Всегда готов!',
        ],
        'bite' => [
            'Сам такой',
            'Я все слышу',
            'Сам ты отстой',
        ],
        'fuck' => [
            'Не дождетесь',
            'Живее всех живых',
        ],
    ];

    /**
     * @var string[]
     */
    static $toBeSaid = [
        '/run' => 'Адрес|Дата|Начало игры|Лимит игроков|Арендная плата|Состав игроков',
    ];

    /**
     * @var string[]
     */
    static $admins = [
        'npnrus',
    ];

    /**
     * @var string[]
     */
    static $titles = [
        'Новая игра! Записываемся!',
        'Адрес: ',
        'Дата: ',
        'Начало игры: ',
        'Лимит игроков: ',
        'Арендная плата: ',
        'Состав игроков:',
    ];

    /**
     * @var string
     */
    protected $keyboard = [];

    /**
     * @var string
     */
    protected $adminKeyboard = [];

    /**
     * @var array
     */
    protected $replyMarkup = [];

    /**
     * @var int
     */
    protected $playersLimit = 1000;

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function newSchedule()
    {
        $isReply = isset($this->message['reply_to_message']);
        $attributes = [];

        if (!$isReply) {
            $this->text = implode(chr(10), static::$titles);
            $this->replyMarkup = [
                'inline_keyboard' => $this->keyboard,
                'resize_keyboard' => true,
            ];

            $this->deleteMessage(false);
            $this->closeOldGame();

            $response = $this->sendMessage();
            $this->pinChatMessage($response['message_id']);

            $attributes['chat_id'] = $this->chatId;
            $attributes['message_id'] = $response['message_id'];
            $attributes['pinned'] = 1;
            $attributes['text'] = $this->text;
            app(TelegramFootbot::class)->create($attributes);
        } else {
            $currentGame =
                app(TelegramFootbot::class)
                    ->where('chat_id', $this->chatId)
                    ->where('status', 'new')
                    ->latest()
                    ->first();
            $text = explode(chr(10), $currentGame->text);
            $messageId = $currentGame->message_id;
            $players = $this->getPlayers($currentGame->text);

            if ($this->said('Адрес')) {
                $attributes['address'] = $this->message['text'];
                $text[1] = static::$titles[1] . $attributes['address'];
            }

            if ($this->said('Дата')) {
                $attributes['date'] = $this->message['text'];
                $text[2] = static::$titles[2] . $attributes['date'];
            }

            if ($this->said('Начало игры')) {
                $attributes['time'] = $this->message['text'];
                $text[3] = static::$titles[3] . $attributes['time'];
            }

            if ($this->said('Лимит игроков')) {
                $attributes['players_limit'] = $this->message['text'];
                $text[4] = static::$titles[4] . $attributes['players_limit'];
            }

            if ($this->said('Арендная плата')) {
                if (is_numeric($this->message['text'])) {
                    $attributes['price'] = $this->message['text'];
                    $text[5] = static::$titles[5] . $attributes['price'];
                }
            }

            if ($this->said('Состав игроков')) {
                $text = array_slice($text, 0, 7);
                $text =
                    array_merge(
                        $text,
                        explode(chr(10), $this->message['text'])
                    );
            }

            $attributes['text'] = implode(chr(10), $text);

            $currentGame->update($attributes);

            $this->deleteMessage();

            $this->editMessageText($players, $attributes['text'], $messageId);
        }
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function adminKeyboard()
    {
        $this->text = 'Для админов';
        $this->replyMarkup = [
            'keyboard' => $this->adminKeyboard,
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];

        $this->sendMessage();
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setAddress()
    {
        $this->setText(1, 'Введи адрес...');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setDate()
    {
        $this->setText(2, 'Введи дату...');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setTime()
    {
        $this->setText(3, 'Введи время...');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setLimit()
    {
        $this->setText(4, 'Введи лимит игроков...');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setPrice()
    {
        $this->setText(5, 'Введи арендную плату...');
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setPlayers()
    {
        $this->setText(null, 'Введи весь текст...');
    }

    /**
     * @param mixed $key
     * @param string $placeholder
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function setText($key, string $placeholder)
    {
        if (!$this->isAdmin) {
            if ($this->callbackId) {
                $this->telegram->answerCallbackQuery([
                    'callback_query_id' => $this->callbackId,
                    'text' => 'У вас нет доступа к этой функции!',
                    'show_alert' => true,
                ]);
            }
            $this->deleteMessage();

            return;
        }

        $this->text = $key ? static::$titles[$key] : 'Состав игроков';
        $this->replyMarkup = [
            'force_reply' => true,
            'input_field_placeholder' => $placeholder,
        ];

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $this->callbackId,]);
        }

        $this->sendMessage();
        $this->deleteMessage();
    }

    /**
     * @param string|null $text
     * @return array
     */
    protected function getTitle(string $text = null): array
    {
        $text = $text ?: $this->message['text'];
        $text = explode(chr(10) . 'Состав игроков:', $text);
        $title = explode(chr(10), $text[0]);
        $title[] = 'Состав игроков:';

        return array_values($title);
    }

    /**
     * @param string $type
     * @return array
     */
    protected function getKeyboard(string $type = 'user'): array
    {
        $keyboard = [];
        $buttons = [];

        if ($type == 'user') {
            $buttons['my'] = static::$myCommands;
            $buttons['guest'] = static::$guestCommands;
        } elseif ($type == 'admin') {
            $buttons['admin1'] = static::$adminCommands1;
            $buttons['admin2'] = static::$adminCommands2;
        }

        foreach ($buttons as $group) {
            $subSet = [];
            foreach ($group as $command => $text) {
                $subSet[] = ['text' => $text, 'callback_data' => $command];
            }

            $keyboard[] = $subSet;
        }

        return $keyboard;
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
     * @param string|null $text
     * @param string|null $chatId
     * @param bool $parseHtml
     * @return Message
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function sendMessage(string $text = null, string $chatId = null, bool $parseHtml = true): Message
    {
        $params = [];
        $params['text'] = $text ?: $this->text;
        $params['chat_id'] = $chatId ?: $this->chatId;

        if (isset($this->replyMarkup) && is_array($this->replyMarkup)) {
            $params['reply_markup'] = json_encode($this->replyMarkup);
        }

        if ($parseHtml) {
            $params['parse_mode'] = 'HTML';
        }

        return $this->telegram->sendMessage($params);
    }

    /**
     * @param array $players
     * @param string|null $text
     * @param int|null $messageId
     * @param bool $hasMarkup
     * @param bool $parseHtml
     * @return bool|Message
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function editMessageText(
        array  $players,
        string $text = null,
        int    $messageId = null,
        bool   $hasMarkup = true,
        bool   $parseHtml = true
    )
    {
        $params = [];
        $params['chat_id'] = $this->chatId;
        $params['message_id'] = $messageId ?: $this->message['message_id'];

        if ($parseHtml) {
            $params['parse_mode'] = 'HTML';
        }

        if ($hasMarkup) {
            $replyMarkup =
                [
                    'inline_keyboard' => $this->keyboard,
                    'resize_keyboard' => true,
                ];
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        if ($text) {
            $this->getShare($text);
            $params['text'] = $text;

            return $this->telegram->editMessageText($params);
        }

        $tmp = $this->getTitle();

        foreach ($players as $k => $player) {
            $tmp[] = ($k + 1) . ". $player";
        }

        $params['text'] = implode(chr(10), $tmp);

        $this->getShare($params['text']);

        app(TelegramFootbot::class)
            ->where('message_id', $this->message['message_id'])
            ->update(['text' => $params['text']]);

        return $this->telegram->editMessageText($params);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function closeOldGame($deleteMessage = false)
    {
        if (!$lastGame =
            app(TelegramFootbot::class)
                ->where('chat_id', $this->chatId)
                ->where('status', 'new')
                ->latest()
                ->first()) {
            return;
        }

        $text = explode(chr(10), $lastGame->text);
        $text[0] = 'Запись закрыта.';
        $text = implode(chr(10), $text);

        $lastGame->update(['status' => 'old', 'pinned' => 0, 'text' => $text]);

        $this->telegram->unpinChatMessage([
            'chat_id' => $this->chatId,
            'message_id' => $lastGame->message_id,
        ]);

        $this->editMessageText([], $text, $lastGame->message_id, false);

        if ($deleteMessage) {
            $this->deleteMessage();
        }
    }

    /**
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function addMe()
    {
        return $this->addPlayer($this->userName);
    }

    /**
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function addMeMaybe()
    {
        return $this->addPlayer($this->userName . ' +/-');
    }

    /**
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function addMyGuest()
    {
        return $this->addPlayer($this->userName . ' (друг)');
    }

    /**
     * @param string $name
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function addPlayer(string $name)
    {
        $players = $this->getPlayers();
        $title = $this->getTitle();
        $limit = explode(':', $title[4]);
        $limit = trim($limit[1]);
        $this->playersLimit = $limit ?: $this->playersLimit;
        $me = $this->userName;
        $meMaybe = $this->userName . ' +/-';
        $guest = $this->userName . ' (друг)';
        $alert = '';
        $addable = true;

        if ($name != $guest && in_array($name, $players)) {
            $alert = 'Вы уже есть в списке!';
            $addable = false;
        } elseif (
            count($players) < $this->playersLimit ||
            (count($players) == $this->playersLimit && $name == $me && in_array($meMaybe, $players)) ||
            (count($players) == $this->playersLimit && $name == $meMaybe && in_array($me, $players))
        ) {
            if ($name == $me && !in_array($meMaybe, $players) && !in_array($me, $players)) {
                $players[] = trim($name);
            } elseif ($name == $me && in_array($meMaybe, $players)) {
                $k = array_search($meMaybe, $players);
                $players[$k] = trim($name);
            } elseif ($name == $meMaybe && !in_array($meMaybe, $players) && !in_array($me, $players)) {
                $players[] = trim($name);
            } elseif ($name == $meMaybe && in_array($me, $players)) {
                $k = array_search($me, $players);
                $players[$k] = trim($name);
            } elseif ($name == $guest) {
                $players[] = trim($name);
            }
        } else {
            if ($this->playersLimit) {
                $alert =
                    'Достигнут лимит игроков: ' . $this->playersLimit . '.' . chr(10) . 'Запись временно остановлена.';
            }

            $addable = false;
        }

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery([
                'callback_query_id' => $this->callbackId,
                'text' => $alert,
                'show_alert' => true,
            ]);
        }

        if (!$addable) {
            return;
        }

        $this->sortPlayers($players);

        return $this->editMessageText($players);
    }

    /**
     * @param string|null $text
     * @return array
     */
    protected function getPlayers(string $text = null): array
    {
        $text = $text ?: $this->message['text'];
        $text = explode('Состав игроков:' . chr(10), $text);

        if (count($text) < 2) {
            return [];
        }

        $players = explode(chr(10), $text[1]);

        return array_map(
            function ($player) {
                return preg_replace('/^\d+([.\s]+)+(@[a-z0-9_]+)(\s)(.+)\s*$/iu', '$2$3$4', $player);
            }, array_values($players)
        );
    }

    /**
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function removeMe()
    {
        $me = [
            $this->userName,
            $this->userName . ' +/-',
        ];

        $mocks = [
            'Струсил наш %s',
            '%s слился.',
        ];

        $isOut = array_rand(array_flip($mocks));

        return $this->removePlayers($me, sprintf($isOut, $this->userName));
    }

    /**
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function removeMyGuest()
    {
        return $this->removePlayers($this->userName . ' (друг)');
    }

    /**
     * @param mixed $names
     * @param string|null $notify
     * @return bool|Message|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function removePlayers($names, string $notify = null)
    {
        $names = !is_array($names) ? [$names] : $names;
        $players = $this->getPlayers();
        $removable = false;

        foreach ($names as $player) {
            if (in_array($player, $players)) {
                $k = array_search($player, $players);
                unset($players[$k]);
                $removable = true;
            }
        }

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $this->callbackId,]);
        }

        if (!$removable) {
            return;
        }

        if ($notify) {
            $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $notify]);
        }

        $this->sortPlayers($players);

        return $this->editMessageText($players);
    }

    /**
     * @param $players
     */
    protected function sortPlayers(&$players)
    {
        usort($players, function ($a, $b) {
            if (strpos($a, '+/-')) {
                if (strpos($b, '+/-')) {
                    return $a<=>$b;
                } else {
                    return 1;
                }
            } else {
                if (strpos($b, '+/-')) {
                    return -1;
                } else {
                    return $a<=>$b;
                }
            }
        });
    }

    /**
     * @param string $text
     */
    protected function getShare(string &$text)
    {
        $text = $text ?: $this->message['text'];
        $tmp = explode(chr(10), $text);

        if ($tmp[5] != static::$titles[5]) {
            $players = $this->getPlayers($text);
            $pattern = '/' . static::$titles[5] . '|\(\w+\)/iu';
            $price = (int)preg_replace($pattern, '', $tmp[5]);
            $tmp[5] = static::$titles[5] . $price;

            if (count($players)) {
                $share = ceil($price / count($players) / 10) * 10;
                $tmp[5] .= " ($share на игрока)";
            }

            $text = implode(chr(10), $tmp);
        }
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function toJoke()
    {
        $this->telegram->sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);

        $page = rand(1, 15);
        $url =
            $page == 1 ?
                env('JOKES_URL') :
                preg_replace('/^(.+)(.html)$/', '$1-' . $page . '$2', env('JOKES_URL'));
        $url = Url::secure($url);
        $html = Http::get($url);
        preg_match_all('~<div id="text">.+?</div>~u', $html, $jokes);
        $key = rand(0, count($jokes[0]));
        $joke = str_replace(['<br>', '<br/>', '<br />'], chr(10), $jokes[0][$key]);
        $joke = strip_tags(html_entity_decode($joke));

        $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $joke]);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function toShout()
    {
        $fuck = rand(0, count(static::$toShout['fuck']) - 1);
        $bite = rand(0, count(static::$toShout['bite']) - 1);
        $random = rand(0, count(static::$toShout['random']) - 1);

        if (strpos($this->message['text'], 'сдох') !== false) {
            $text = static::$toShout['fuck'][$fuck];
        } elseif (strpos($this->message['text'], 'отстой') !== false) {
            $text = static::$toShout['bite'][$bite];
        } else {
            $text = static::$toShout['random'][$random];
        }

        $this->telegram->sendMessage(['chat_id' => $this->chatId, 'text' => $text]);
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function info()
    {
        $this->text =
            'Этот бот организует запись на предстоящую игру чата' . chr(10) .
            '/start, /menu или "Футбот, новая игра" (в любом регистре, можно без запятой) - начать новую запись.' .
            chr(10) .
            'Список кнопок:' . chr(10) .
            '"Я+" - точно приду, добавляюсь' . chr(10) .
            '"Я+/-" - не точно, 50/50, но добавляюсь' . chr(10) .
            '"Друг+" - добавляю друга (1 клик - 1 друг) ' . chr(10) .
            '"Я-" - ой, не получится, сливаюсь' . chr(10) .
            '"Друг-" - друг не придет (1 клик - 1 друг)';

        $this->sendMessage();
    }
}
