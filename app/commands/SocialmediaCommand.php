<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class SocialmediaCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'socialmedia';
	/**
	 * @var string
	 */
	protected $description = 'Show social media with information you can use to';
	/**
	 * @var string
	 */
	protected $usage = '/socialmedia - Show social media with information you can use to';
	
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
		
		Request::sendChatAction([
			'chat_id' => $chat_id,
			'action' => ChatAction::TYPING,
		]);
		
		$text = 'Social Media:';
		$text .= PHP_EOL . PHP_EOL;
		
		if (CatBot::app()->config->get('telegram_group_to_follow_link_url')){
			$text .= '<a href="'. CatBot::app()->config->get('telegram_group_to_follow_link_url') .'">Telegram Group</a>';
			$text .= PHP_EOL;
		}
		if (CatBot::app()->config->get('telegram_chanel_to_follow_link_url')){
			$text .= '<a href="'. CatBot::app()->config->get('telegram_chanel_to_follow_link_url') .'">Telegram Chanel</a>';
			$text .= PHP_EOL;
		}
		if (CatBot::app()->config->get('twitter_profile_url')){
			$text .= '<a href="'. CatBot::app()->config->get('twitter_profile_url') .'">Twitter</a>';
			$text .= PHP_EOL;
		}
		
		$keyboard = new Keyboard(
			[
				['text' => '/balance 💰'],
				['text' => '/referrallink 👥'],
			],
			[
				['text' => '/support ☎'],
				['text' => '/socialmedia 🔗']
			]
		);
		$keyboard->setResizeKeyboard(true);
		
		$data = [
			'chat_id' => $chat_id,
			'text' => $text,
			'parse_mode' => 'html'
		];
		
		if (isset($keyboard)) {
			$data['reply_markup'] = $keyboard;
		}
		
		return Request::sendMessage($data);
	}
}