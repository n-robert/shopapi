<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Telegram\Bot\Api;

class LeadSuTestController extends Controller
{
    /**
     * @var array
     */
    static $commands =
        [
            '/getUser' => 'Get user',
            '/getUserReports' => 'Get reports',
            '/getLast10Countries' => 'Get countries',
        ];
    /**
     * @var array
     */
    static $infoFields =
        [
            'id' => 'ID',
            'name' => 'Name',
            'display_name' => 'Display name',
            'phone' => 'Phone number',
            'email' => 'E-mail',
        ];
    /**
     * @var array
     */
    static $reportFields =
        [
            'period_hour' => 'Period',
            'impressions' => 'Impressions',
            'unique_impressions' => 'Unique impressions',
            'clicks' => 'Clicks',
            'unique_clicks' => 'Unique clicks',
            'conversions_approved' => 'Approved conversions',
            'payout' => 'Payout',
            'offer_id' => 'Offer ID',
            'goal_id' => 'Goal ID',
            'source' => 'Source',
        ];
    /**
     * @var Api
     */
    protected $telegram;
    /**
     * @var int
     */
    protected $chatId;
    /**
     * @var string
     */
    protected $userName;
    /**
     * @var string
     */
    protected $text;
    /**
     * @var array
     */
    protected $replyMarkup;
    /**
     * @var string
     */
    protected $webmasterToken;
    /**
     * @var string
     */
    protected $webmasterApiUrl;

    /**
     * RobertBotController constructor.
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
        $this->webmasterToken = env('WEBMASTER_TOKEN');
        $this->webmasterApiUrl = env('WEBMASTER_API_URL');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    public function setWebHook()
    {
        $response = $this->telegram->setWebhook(['url' => Url::secure(env('TELEGRAM_WEBHOOK_URL'))]);

        return $response == true ? redirect()->back() : dd($response);
    }

    /**
     * @return void
     */
    public function handleUpdate()
    {
        $update = $this->telegram->getWebhookUpdate();

        if (isset($update['callback_query'])) {
            $message = $update['callback_query']['message'];
            $method = $update['callback_query']['data'];
        } else {
            $message = $update['message'];
            $method =
                Str::startsWith($message['text'], '/') ?
                    $message['text'] :
                    array_search($message['text'], static::$commands);
        }

        $this->chatId = $message['chat']['id'];
        $this->userName = $message['from']['username'];

        switch ($method) {
            case '/start':
            case '/menu':
                $this->showMenu();
                break;
            default:
                $method = str_replace('/', '', $method);

                if (method_exists($this, $method)) {
                    call_user_func([$this, $method]);
                }
        }
    }

    /**
     * @param null $info
     * @return void
     */
    public function showMenu($info = null)
    {
        $this->text = 'Choose an action!';

        if ($info) {
            $this->text .= chr(10) . $info;
        }

        $keyboard =
            [
                [
                    [
                        'text' => 'Get user',
                        'callback_data' => '/getUser',
                    ],
                    [
                        'text' => 'Get reports',
                        'callback_data' => '/getUserReports',
                    ],
                    [
                        'text' => 'Get countries',
                        'callback_data' => '/getLast10Countries',
                    ],
                ]
            ];
        $this->replyMarkup =
            [
                'inline_keyboard' => $keyboard, // 'keyboard' => $keyboard
                'resize_keyboard' => true,
            ];

        $this->sendMessage();
    }

    /**
     * @return void
     */
    public function getUser()
    {
        $params = ['token' => $this->webmasterToken];
        $response = Http::get($this->webmasterApiUrl . '/account', $params);
        $data = $response->json('data');
        $text = [];

        if (!empty($data)) {
            foreach (static::$infoFields as $key => $name) {
                if (isset($data[$key])) {
                    $text[] = $name . ': ' . $data[$key];
                }
            }
        } else {
            $text[] = 'Sorry, there are no data to show yet.';
        }

        $this->text = implode(chr(10), $text);

        $this->sendMessage();
    }

    /**
     * @param null $startDate
     * @param null $endDate
     * @param int $offset
     * @param string $grouping
     * @return void
     */
    public function getUserReports($startDate = null, $endDate = null, $offset = 0, $grouping = 'month')
    {
        $startDate = $startDate ?: Carbon::now();
        $endDate = $endDate ?: Carbon::now()->modify('-24 hours');
        $params =
            [
                'token' => $this->webmasterToken,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'offset' => $offset,
                'grouping' => $grouping,
            ];

        $response = Http::get($this->webmasterApiUrl . '/reports', $params);
        $data = $response->json('data');
        $text = [];

        if (!empty($data)) {
            foreach (static::$reportFields as $key => $name) {
                if (isset($data[$key])) {
                    $text[] = $name . ': ' . $data[$key];
                }
            }
        } else {
            $text[] = 'Sorry, there are no data to show yet.';
        }

        $this->text = implode(chr(10), $text);

        $this->sendMessage();
    }

    /**
     * @return void
     */
    public function getLast10Countries()
    {
        $params = ['token' => $this->webmasterToken];
        $response = Http::get($this->webmasterApiUrl . '/geo/getCountries', $params);
        $data =
            collect($response->json('data'))
                ->sortBy('name', SORT_REGULAR, true)
                ->slice(0, 10)
                ->all();
        $text = [];

        if (!empty($data)) {
            foreach ($data as $country) {
                $text[] = $country['name'] . ' (' . $country['iso_alpha2'] . ')';
            }
        } else {
            $text[] = 'Sorry, there are no data to show yet.';
        }

        $this->text = implode(chr(10), $text);

        $this->sendMessage();
    }

    /**
     * @param null $text
     * @param bool $parse_html
     * @return void
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function sendMessage($text = null, $parse_html = false)
    {
        $data = [];
        $data['chat_id'] = $this->chatId;
        $data['text'] = $text ?: $this->text;

        if (is_array($this->replyMarkup)) {
            $data['reply_markup'] = json_encode($this->replyMarkup);
        }

        if ($parse_html) {
            $data['parse_mode'] = 'HTML';
        }

        $this->telegram->sendMessage($data);
    }
}

