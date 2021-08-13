<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\URL;
use Telegram\Bot\Api;

class TelegramController extends Controller
{
    /**
     * @var array
     */
    static    $sequence     =
        [
            '/typeLogin'    => 'Please, enter your login!',
            '/typePassword' => 'Please, enter your password!',
        ];
    /**
     * @var array
     */
    static    $infoFields   =
        [
            'id'           => 'ID',
            'name'         => 'Name',
            'display_name' => 'Display name',
            'phone'        => 'Phone number',
            'email'        => 'E-mail',
        ];
    /**
     * @var array
     */
    static    $reportFields =
        [
            'period_hour'          => 'Period',
            'impressions'          => 'Impressions',
            'unique_impressions'   => 'Unique impressions',
            'clicks'               => 'Clicks',
            'unique_clicks'        => 'Unique clicks',
            'conversions_approved' => 'Approved conversions',
            'payout'               => 'Payout',
            'offer_id'             => 'Offer ID',
            'goal_id'              => 'Goal ID',
            'source'               => 'Source',
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
     * TelegramController constructor.
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
        $request = $this->telegram->getWebhookUpdate();

        if (isset($request['callback_query'])) {
            $message = $request['callback_query']['message'];
            $method = $request['callback_query']['data'];
        } else {
            $message = $request['message'];
            $method = $message['text'];
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
                        'text'          => 'Get user info',
                        'callback_data' => '/getUserInfo',
                    ],
                    [
                        'text'          => 'Get user reports',
                        'callback_data' => '/getUserReports',
                    ],
                ]
            ];
        $this->replyMarkup =
            [
                'inline_keyboard'   => $keyboard,
                'one_time_keyboard' => true,
                'resize_keyboard'   => true,
            ];

        $this->sendMessage();
    }

    /**
     * @return void
     */
    public function getUserInfo()
    {
        $params = ['token' => $this->webmasterToken];
        $response = Http::get($this->webmasterApiUrl . '/account', $params);
        $data = $response->json('data');
        $text = [];

        foreach (static::$infoFields as $key => $name) {
            if (isset($data[$key])) {
                $text[] = $name . ': ' . $data[$key];
            }
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
                'token'      => $this->webmasterToken,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'offset'     => $offset,
                'grouping'   => $grouping,
            ];

        $response = Http::get($this->webmasterApiUrl . '/reports', $params);
        $data = $response->json('data');
        $text = [];

        foreach (static::$reportFields as $key => $name) {
            if (isset($data[$key])) {
                $text[] = $name . ': ' . $data[$key];
            }
        }

        $this->text = !empty($text) ? implode(chr(10), $text) : 'Sorry, there are no data to show yet.';

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
