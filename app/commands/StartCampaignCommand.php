<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use app\domain\CampaignHelper;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Entities\Keyboard;

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
		$user_id = $message->getFrom()->getId();
		
		
		Request::sendChatAction([
			'chat_id' => $chat_id,
			'action'  => ChatAction::TYPING,
		]);
		
		$text = '';
		
		if (CatBot::app()->campaignService->isUserHaveAlreadyStartedCampaign($user_id)){
			Request::sendMessage([
				'chat_id' => $chat_id,
				'text'  => "I think you already started a company. Don't piss me off pls.",
				'reply_markup'=> Keyboard::remove()
			]);
		}
		else{
			Request::sendMessage([
				'chat_id' => $chat_id,
				'text'  => "OK. Let's start.",
				'reply_markup'=> Keyboard::remove()
			]);
		}
		
		$campaignStarted = CatBot::app()->campaignService->createNewUserCampaign($user_id);
		
		if ($campaignStarted){
			$keyboard = new InlineKeyboard(CampaignHelper::getJoinToKeyboardArray(
				CatBot::app()->config->get('telegram_group_to_follow_link_url'),
				CatBot::app()->config->get('telegram_channel_to_follow_link_url')
			));
			
			Request::sendMessage([
				'chat_id' => $chat_id,
				'text'  => 'First of all - you need to join our channel and group.',
				'reply_markup'=> $keyboard
			]);
			
			$keyboard = new Keyboard([
				['text' => '/checkme']
			]);
			$keyboard->setResizeKeyboard(true);
			$keyboard->setOneTimeKeyboard(true);
			
			Request::sendMessage([
				'chat_id' => $chat_id,
				'text'  => 'After this type /checkme and i\'ll check you really done this.',
				'reply_markup'=> $keyboard
			]);
		}
		else{
			$text = 'Oh shit, i think i have broken database. 💀'
					. PHP_EOL .
					'Sorry, but i can not make you happy with 🐱 tokens right now. 😿'
					. PHP_EOL .
					'Try again later. ⌛';
		}
		
		$data = [
			'chat_id' => $chat_id,
			'text'    => $text
		];
		
		if (isset($keyboard)){
			$data['reply_markup'] = $keyboard;
		}
	
		return Request::sendMessage($data);
	}
}