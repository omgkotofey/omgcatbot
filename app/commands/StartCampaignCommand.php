<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\Bot;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class StartCampaignCommand extends UserCommand
{
	/**
	* @var string
	*/
	protected $name = 'startcampaign';
	/**
	 * @var string
	 */
	protected $description = 'Start our token earning campaign';
	/**
	 * @var string
	 */
	protected $usage = '/startcampaign - Start our token campaign, fulfill all conditions and earn FREE tokens';
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
		$user_id = $message->getChat()->getUsername();
		
		$keyboard = new InlineKeyboard([
			['text' => 'Follow our Twitter', 'url' => Bot::app()->config->get('twitter_profile_to_follow_url')]
		]);
		$keyboard->setResizeKeyboard(true);
		$keyboard->setOneTimeKeyboard(true);
		
		$text    =  'Follow us on Twitter, and retweet last post - Paste link to tweet below:';
		$data = [
			'chat_id' => $chat_id,
			'text'    => $text,
			'reply_markup' => $keyboard,
		];
		
		return Request::sendMessage($data);
	}
}