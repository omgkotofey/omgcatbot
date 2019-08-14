<?php

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
/**
 * Start command
 *
 * Gets executed when a user first starts using the bot.
 */
class StartCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'start';
	/**
	 * @var string
	 */
	protected $description = 'Start command';
	/**
	 * @var string
	 */
	protected $usage = '/start - Just start this damn bot';
	/**
	 * @var bool
	 */
	protected $private_only = true;
	/**
	 * Command execute method
	 *
	 * @return ServerResponse
	 * @throws TelegramException
	 */
	public function execute()
	{
		$message = $this->getMessage();
		$chat_id = $message->getChat()->getId();
		
		$keyboard = new Keyboard([
			['text' => '/startcampaign']
		]);
		$keyboard->setResizeKeyboard(true);
		$keyboard->setOneTimeKeyboard(true);
		
		$text    =  'Hello, leather bastard! I am the CatBot. 🐈'
					. PHP_EOL .
					'If you completely fulfill all my conditions, you will receive 10 🐱 tokens for any actions that do not annoy me...'
					. PHP_EOL .
					'Press "/startcampaign" button to start the CatBot campaign 🚀:';
		$data = [
			'chat_id' => $chat_id,
			'text'    => $text,
			'reply_markup' => $keyboard,
		];
		return Request::sendMessage($data);
	}
}