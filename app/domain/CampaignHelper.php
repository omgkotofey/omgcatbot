<?php


namespace app\domain;

class CampaignHelper
{
	/**
	 * Get first found link form text
	 *
	 * @param string $text
	 *
	 * @return string|false
	 */
	public static function getTwitterLinkFromText(string $text){
		preg_match('/^https?:\/\/twitter\.com/', $text, $matches);
		if (!empty($matches)){
			return reset($matches);
		}
		return false;
	}
	
	/**
	 * Get first ethereum address form text
	 *
	 * @param string $text
	 *
	 * @return string|false
	 */
	public static function getEthereumAddressFromText(string $text){
		preg_match('/^0x[a-fA-F0-9]{40}/', $text, $matches);
		if (!empty($matches)){
			return reset($matches);
		}
		return false;
	}
	
	/**
	 * Return user referral link
	 *
	 * @param string $user_id
	 * @param string $bot_name
	 *
	 * @return string
	 */
	public static function getUserReferralLink(string $user_id, string $bot_name){
		$unique_id = uniqid();
		return "http://t.me/$bot_name?start=$unique_id";
	}

}