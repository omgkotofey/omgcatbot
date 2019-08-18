<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use app\utils\KeyboardHelper;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class BalanceCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'balance';
	/**
	 * @var string
	 */
	protected $description = 'Show your balance in tokens';
	/**
	 * @var string
	 */
	protected $usage = '/balance - Show your balance in tokens';
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
		
		$keyboard = KeyboardHelper::getEmptyKeyboard();
		
		Request::sendChatAction([
			'chat_id' => $chat_id,
			'action' => ChatAction::TYPING,
		]);
		
		if (!CatBot::app()->campaignService->isUserHaveAlreadyStartedCampaign($user_id)) {
			$text = "I think you did not started our campaign yet. Type /startcampaign to start it.";
			$keyboard = KeyboardHelper::getStartCampaignKeyboard();
		} else {
			
			$user_campaign = CatBot::app()->campaignService->getActiveUserCampaign($user_id);
			
			if (!empty($user_campaign->getEthereumAddress())){
				$user_tokens_count = $user_campaign->getTokensEarnedCount();
				$text = "0 Partners 👥";
				$text .= PHP_EOL;
				$text .= "$user_tokens_count 🐱 tokens earned";
				$keyboard = KeyboardHelper::getMainMenuKeyboard();
			} else {
				$text = "You can not check your token balance yet! Fulfill all all my previous conditions to make it real.";
			}
		}
		
		$data = [
			'chat_id' => $chat_id,
			'text' => $text,
			'reply_markup' => $keyboard
		];
		
		return Request::sendMessage($data);
	}
}