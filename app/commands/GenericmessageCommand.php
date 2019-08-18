<?php

namespace Longman\TelegramBot\Commands\SystemCommands;

use app\core\CatBot;
use app\domain\CampaignHelper;
use app\utils\BotDevelopmentHelper;
use Longman\TelegramBot\ChatAction;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Entities\ChatMember;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
	/**
	 * @var string
	 */
	protected $name = 'genericmessage';
	
	/**
	 * @var string
	 */
	protected $description = 'Handle generic message';
	
	
	
	
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
		
		if ($chat_id != CatBot::app()->config->get('telegram_group_to_follow_id')){
			
			$user_campaign = CatBot::app()->campaignService->getActiveUserCampaign($user_id);
			
			$text = 'I don\'t understand what are you want for me! 😞'. PHP_EOL . PHP_EOL;
			$text .= 'Start our campaign by by pressing /startcampaign first if you still did not ';
			$text .= 'or just follow previous instructions carefully'. PHP_EOL . PHP_EOL;
			$text .= 'And don\'t waste my time pls! 😾';
			
			Request::sendChatAction([
				'chat_id' => $chat_id,
				'action'  => ChatAction::TYPING,
			]);
			
			if (!empty($user_campaign) && $user_campaign->getIsFollower()){
					$message_text = trim($message->getText(true));
					
					$any_link = CampaignHelper::getTwitterLinkFromText($message_text);
					$any_wallet = CampaignHelper::getEthereumAddressFromText($message_text);
					
					if (!empty($any_link) && empty($user_campaign->getHasRetweet())){
						$text = 'Looks like Twitter link. Thank you for retweet.'. PHP_EOL . PHP_EOL;
						
						$user_campaign->setHasRetweet(1);
						$user_campaign->setTwitterLink($any_link);
						
						if (CatBot::app()->campaignService->updateCampaign($user_campaign)){
							$text .= 'Now we need to get your Ethereum address (Address needs to begin with 0x and needs to be ERC 20 Compatible)';
						}
					}
					
					if (!empty($any_wallet) && $user_campaign->getHasRetweet() && empty($user_campaign->getEthereumAddress())){
						$text = 'Looks like ethereum wallet address.'. PHP_EOL . PHP_EOL;
						
						$user_campaign->setEthereumAddress($any_wallet);
						$user_campaign->setHasTokensEarned(1);
						$user_campaign->setTokensEarnedCount(10);
						$user_campaign->setRefLink(CampaignHelper::getUniqueReferralLink(CatBot::app()->config->get('bot_username')));
						
						if (CatBot::app()->campaignService->updateCampaign($user_campaign)){
							$text .= 'Thanks! Your details have been submitted successfully.';
							$text .=  PHP_EOL . PHP_EOL;
							$text .= 'Congratulations, you have earned 10 🐱 tokens! ';
							$text .= 'The following details have been logged:';
							$text .=  PHP_EOL . PHP_EOL;
							$text .= 'Address - ' . $user_campaign->getEthereumAddress();
							$text .=  PHP_EOL;
							$text .= 'Retweet - ' . $user_campaign->getTwitterLink();
							$text .=  PHP_EOL . PHP_EOL;
							$text .= 'Your unique referral link is: ' . $user_campaign->getRefLink();
							$text .=  PHP_EOL . PHP_EOL;
							$text .= 'Share and forward the referral link to your network and get 10 🐱 tokens for each friend invited!';
							$text .= 'They will have to join our chat and stay until the end of the Bounty campaign you to receive the reward!';
							$text .= 'Users who get caught cheating will be disqualified.';
							$text .=  PHP_EOL . PHP_EOL;
							$text .= 'Press /help to know commands you can use to interact';
							
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
						}
					}
					
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
}