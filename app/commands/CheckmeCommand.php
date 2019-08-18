<?php


namespace Longman\TelegramBot\Commands\UserCommands;


use app\core\CatBot;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\ChatMember;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

class CheckmeCommand extends UserCommand
{
	/**
	 * @var string
	 */
	protected $name = 'checkme';
	/**
	 * @var string
	 */
	protected $description = 'Checks do you are already joined all necessary groups and channel';
	/**
	 * @var string
	 */
	protected $usage = '/checkme - Checks do you are already joined all necessary groups and channels';
	/**
	 * @var bool
	 */
	protected $private_only = true;
	
	private function checkUserIsMemberOfChat($user_id, $chat_id)
	{
		$isMemberRequest = Request::getChatMember(['chat_id' => $chat_id, 'user_id' => $user_id]);
		if ($isMemberRequest->isOk()) {
			/**
			 * @var $isMemberResult ChatMember
			 */
			$isMemberResult = $isMemberRequest->getResult();
			$memberStatus = $isMemberResult->getStatus();
			return in_array($memberStatus, ['member', 'administrator', 'creator']);
		}
		return false;
	}
	
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
			$keyboard = Keyboard::remove();
		} else {
			
			$user_campaign = CatBot::app()->campaignService->getActiveUserCampaign($user_id);
			
			$must_joined = [
				'group' => $this->checkUserIsMemberOfChat($user_id, CatBot::app()->config->get('telegram_group_to_follow_id'))
			];
			
			if (CatBot::app()->config->get('telegram_channel_to_follow_id')) {
				$must_joined['channel'] = $this->checkUserIsMemberOfChat($user_id, CatBot::app()->config->get('telegram_channel_to_follow_id'));
			}
			
			if (!in_array(false, $must_joined)) {
				$user_campaign->setIsFollower(1);
				CatBot::app()->campaignService->updateCampaign($user_campaign);
				Request::sendMessage([
					'chat_id' => $chat_id,
					'text'  => 'OK. I see - you done it!',
					'reply_markup'=> Keyboard::remove()
				]);
				$text = 'Now you need to retweet last tweet from our Twitter profile and paste link to your retweet below:';
				$keyboard = new InlineKeyboard([
					[ 'text' => 'Retweet last tweet', 'url' => CatBot::app()->config->get('twitter_profile_url')]
				]);
			} else {
				$keyboard = new Keyboard([
					['text' => '/checkme']
				]);
				$keyboard->setResizeKeyboard(true);
				$keyboard->setOneTimeKeyboard(true);
				$text = "Em...Nope. You did not join ";
				if (!$must_joined['group']) {
					$text .= 'our group ';
				}
				if (isset($must_joined['channel']) && !$must_joined['channel']) {
					if (!$must_joined['group']) {
						$text .= 'and ';
					}
					$text .= 'our channel';
				}
				$text .= '!' . PHP_EOL . PHP_EOL . 'Join it and type /checkme again.' . PHP_EOL . ' If you forgot WTF is going on type /startcampaign';
			}
		}
		
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