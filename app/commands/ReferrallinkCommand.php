<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class ReferrallinkCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'referrallink';
	/**
	 * @var string
	 */
	protected $description = 'Show your referral link';
	/**
	 * @var string
	 */
	protected $usage = '/referrallink - Show your referral link';
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
		$user_id = $message->getFrom()->getId();
		
		Request::sendChatAction([
			'chat_id' => $chat_id,
			'action' => ChatAction::TYPING,
		]);
		
		if (!CatBot::app()->campaignService->isUserHaveAlreadyStartedCampaign($user_id)) {
			$text = "I think you did not started our campaign yet. Type /startcampaign to start it.";
		} else {
			
			$user_campaign = CatBot::app()->campaignService->getActiveUserCampaign($user_id);
			
			if (!empty($user_campaign->getRefLink())){
				$text = "Earn 10 🐱 Token for every partner.";
				$text .= PHP_EOL . PHP_EOL;
				$text .= "Share this link with your friends:";
				$text .= PHP_EOL;
				$text .= $user_campaign->getRefLink();
			} else {
				$text = "I can not show your referral link yet! Fulfill all all my previous conditions to make it real.";
			}
		}
		
		$keyboard = Keyboard::remove();
		
		$data = [
			'chat_id' => $chat_id,
			'text' => $text
		];
		
		if (isset($keyboard)) {
			$data['reply_markup'] = $keyboard;
		}
		
		return Request::sendMessage($data);
	}
}