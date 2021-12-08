<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

trait TheMealDb
{
    static $methodAlias = [
        'start' => [
            'method' => 'getMenu',
        ],
        'menu' => [
            'method' => 'getMenu',
        ],
        'categories' => [
            'method' => 'getCategories',
        ],
        'mealsbycat' => [
            'method' => 'getMealsByCategory',
        ],
        'mealbyname' => [
            'method' => 'getMealById',
        ],
        'mealbyingr' => [
            'method' => 'getMealByIngredients',
        ],
    ];

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function getMenu()
    {
        $buttons = [
            [['text' => 'Categories'], ['text' => 'Meal by first letter']],
            [['text' => 'Meal by name'], ['text' => 'Meal by ingredients']],
        ];

        $replyMarkup = [
            'keyboard' => $buttons,
            'resize_keyboard' => true,
        ];

        $params = [];
        $params['chat_id'] = $this->chatId;
        $params['text'] = "Let's cook";
        $params['parse_mode'] = 'HTML';
        $params['reply_markup'] = json_encode($replyMarkup);

        $this->telegram->sendMessage($params);
        $this->deleteMessage();
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function getCategories()
    {
        $categories = json_decode(Http::get("$this->endpoint/list.php?c=list"));
        $buttons = [];
        $i = -1;

        foreach ($categories->meals as $k => $category) {
            if ($k % 3 == 0) {
                $i++;
            }

            $button = ['text' => $category->strCategory, 'callback_data' => "mealsbycat|$category->strCategory",];
            $buttons[$i][] = $button;
        }

        $replyMarkup = [
            'inline_keyboard' => $buttons,
            'resize_keyboard' => true,
        ];

        $params = [];
        $params['chat_id'] = $this->chatId;
        $params['text'] = 'All categories';
        $params['parse_mode'] = 'HTML';
        $params['reply_markup'] = json_encode($replyMarkup);

        $response = $this->telegram->sendMessage($params);
        $this->pinChatMessage($response['message_id']);
        $this->deleteMessage();

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $this->callbackId,]);
        }
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function getMealsByCategory()
    {
        $category = explode('|', $this->callbackData);
        $category = trim($category[1]);
        $meals = json_decode(Http::get("$this->endpoint/filter.php?c=$category"));
        $buttons = [];

        foreach ($meals->meals as $meal) {
            $button = [];
            $button[] = ['text' => $meal->strMeal, 'callback_data' => "mealbyname|$meal->idMeal",];
            $buttons[] = $button;
        }

        $replyMarkup = [
            'inline_keyboard' => $buttons,
            'resize_keyboard' => true,
        ];

        $params = [];
        $params['chat_id'] = $this->chatId;
        $params['text'] = "All meals of category \"$category\"";
        $params['parse_mode'] = 'HTML';
        $params['reply_markup'] = json_encode($replyMarkup);

        $response = $this->telegram->sendMessage($params);
        $this->pinChatMessage($response['message_id']);

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $this->callbackId,]);
        }
    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function getMealById()
    {
        $mealId = explode('|', $this->callbackData)[1];
        $meal = json_decode(Http::get("$this->endpoint/lookup.php?i=$mealId"));
        $meal = $meal->meals[0];

        $text = [];
        $text[] = "<a href=\"$meal->strMealThumb\"><b>$meal->strMeal</b></a>";
        $text[] = "<b>Category:</b> $meal->strCategory";
        $text[] = "<b>Area:</b> $meal->strArea";
        $text[] = "<b>Instruction:</b>";
        $text[] = $meal->strInstructions;
        $text[] = "<b>Ingredients:</b>";
        $k = 1;

        while (!empty($meal->{"strIngredient$k"})) {
            $ingredient = $meal->{"strIngredient$k"};
            $measure = $meal->{"strMeasure$k"};
            $text[] = " - $ingredient: $measure";
            $k++;
        }

        $params = [];
        $params['chat_id'] = $this->chatId;
        $params['text'] = implode(chr(10), $text);
        $params['parse_mode'] = 'HTML';

        $this->telegram->sendMessage($params);

    }

    /**
     * @throws \Telegram\Bot\Exceptions\TelegramSDKException
     */
    protected function getMealByIngredients()
    {
        $isReply = isset($this->message['reply_to_message']);

        if (!$isReply) {
            $replyMarkup = [
                'force_reply' => true,
                'input_field_placeholder' => 'Type in ingredients...',
            ];

            $params = [];
            $params['chat_id'] = $this->chatId;
            $params['text'] = 'Ingredients';
        } else {
            $ingredients = $this->message['text'];
            $meals = json_decode(Http::get("$this->endpoint/filter.php?i==$ingredients"));
            $buttons = [];

            foreach ($meals->meals as $meal) {
                $button = [];
                $button[] = ['text' => $meal->strMeal, 'callback_data' => "mealbyname|$meal->strMeal",];
                $buttons[] = $button;
            }

            $replyMarkup = [
                'inline_keyboard' => $buttons,
                'resize_keyboard' => true,
            ];

            $params = [];
            $params['chat_id'] = $this->chatId;
            $params['text'] = "All meals with $ingredients";

        }

        $params['parse_mode'] = 'HTML';
        $params['reply_markup'] = json_encode($replyMarkup);
        $this->telegram->sendMessage($params);

        if ($this->callbackId) {
            $this->telegram->answerCallbackQuery(['callback_query_id' => $this->callbackId,]);
        }
    }
}
