<?php


namespace app\domain;


use app\core\CatBotDB;
use \Exception;

class CampaignService
{
	/**
	 * Build new Campaign entity form assoc array
	 *
	 * @param $campaign_data array
	 *
	 * @return Campaign
	 */
	private function buildCampaign(array $campaign_data){
		return new Campaign($campaign_data);
	}
	
	/**
	 * Create new campaign for user
	 *
	 * @param $user_id
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function createNewUserCampaign($user_id)
	{
		if (!$this->isUserHaveAlreadyStartedCampaign($user_id)){
			return CatBotDB::insertCampaign($user_id);
		}
		return true;
	}
	
	/**
	 * Get all user's campaigns as array of objects
	 *
	 * @param $user_id
	 *
	 * @return array|false
	 * @throws Exception
	 */
	public function getUserCampaigns($user_id)
	{
		$campaigns_as_assoc = CatBotDB::selectCampaign($user_id);
		$campaigns_as_obj = [];
		if (!empty($campaigns_as_assoc)){
			foreach ($campaigns_as_assoc as $campaign_assoc){
				$campaigns_as_obj[] = $this->buildCampaign($campaign_assoc);
			}
		}
		return $campaigns_as_obj;
	}
	
	/**
	 * @param $user_id
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function isUserHaveAlreadyStartedCampaign($user_id)
	{
		$campaigns = $this->getUserCampaigns($user_id);
		if (is_array($campaigns)){
			return count($campaigns) > 0;
		}
		return false;
	}
	
	/**
	 * Get presently active user's campaign
	 *
	 * @param $user_id
	 *
	 * @return Campaign|false
	 * @throws Exception
	 */
	public function getActiveUserCampaign($user_id)
	{
		$campaigns = $this->getUserCampaigns($user_id);
		if (!empty($campaigns)){
			foreach ($campaigns as $campaign){
				/**
				 * @var $campaign Campaign
				 */
				if (empty($campaign->getFinishedAt())){
					return $campaign;
				}
			}
		}
		return false;
	}
	
	/**
	 * Update campaign data in database
	 *
	 * @param $campaign Campaign
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function updateCampaign(Campaign $campaign)
	{
		return CatBotDB::updateCampaign($campaign);
	}
	
	
}