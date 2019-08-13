<?php


namespace app\domain;


use app\core\CatBotDB;
use Longman\TelegramBot\Exception\TelegramException;

class CampaignService
{
	
	/**
	 * @param $user_id
	 *
	 * @return bool
	 * @throws TelegramException
	 */
	public function createNewUserCampaign($user_id)
	{
		if (!$this->isUserHaveAlreadyStartedCampaign($user_id)){
			return CatBotDB::insertCampaign($user_id);
		}
		return true;
	}
	
	/**
	 * @param $user_id
	 *
	 * @return bool
	 * @throws TelegramException
	 */
	public function getUserCampaign($user_id)
	{
		return CatBotDB::selectCampaign($user_id);
	}
	
	/**
	 * @param $user_id
	 *
	 * @return bool
	 * @throws TelegramException
	 */
	public function isUserHaveAlreadyStartedCampaign($user_id)
	{
		$campaigns = $this->getUserCampaign($user_id);
		if (is_array($campaigns)){
			return count($campaigns) > 0;
		}
		return false;
	}
	
	
}