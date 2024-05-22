<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Laravel\Facades\Telegram;

class TelegramBotController extends Controller
{
    public function handle(Request $request)
    {
        $update = Telegram::commandsHandler(true);

        // Check if it's a text message
        if ($update->isType('text')) {
            $chatId = $update->getChat()->id;
            $message = $update->getMessage()->text;

            // Process the message and retrieve user information
            if ($message == '/get_user_info') {
                $user = $update->getMessage()->from;
                $response = "User ID: " . $user->id . "\n";
                $response .= "First Name: " . $user->firstName . "\n";
                $response .= "Last Name: " . $user->lastName . "\n";
                $response .= "Username: @" . $user->username;

                Telegram::sendMessage([
                    'chat_id' => $chatId,
                    'text' => $response,
                ]);
            }
        }
    }
}
