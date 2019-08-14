<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\ChatAction;

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
			$text .= "I think you already started a company. Don't piss me off pls." . PHP_EOL  . PHP_EOL;
		}
		
		$campaignStarted = CatBot::app()->campaignService->createNewUserCampaign($user_id);
		
		
		
		if ($campaignStarted){
			$keyboard = new InlineKeyboard([
				['text' => 'Follow our chanel', 'url' => CatBot::app()->config->get('link_to_follow')],
				['text' => 'Retweet last tweet', 'url' => CatBot::app()->config->get('twitter_profile_to_retweet')]
			]);
			$keyboard->setResizeKeyboard(true);
			$keyboard->setOneTimeKeyboard(true);
			
			$text .= 'Follow our chanel first, then retweet last tweet from our Twitter profile and paste link to your retweet tweet below:';
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