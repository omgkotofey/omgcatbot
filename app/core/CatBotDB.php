<?php


namespace app\core;


use Longman\TelegramBot\DB;
use Longman\TelegramBot\Exception\TelegramException;
use PDO;
use PDOException;

class CatBotDB extends DB
{
	/**
	 * Define all the tables with the proper prefix
	 */
	protected static function defineTables()
	{
		parent::defineTables();
		$tables = [
			'campaign',
		];
		foreach ($tables as $table) {
			$table_name = 'TB_' . strtoupper($table);
			if (!defined($table_name)) {
				define($table_name, self::$table_prefix . $table);
			}
		}
	}
	
	/**
	 * Fetch campaign(s) from DB
	 *
	 * @param string $user_id    Check for unique campaign id
	 * @param int $limit Limit the number of campaigns to fetch
	 *
	 * @return array|bool Fetched data or false if not connected
	 * @throws TelegramException
	 */
	public static function selectCampaign($user_id = null, $limit = null)
	{
		if (!self::isDbConnected()) {
			return false;
		}
		
		try {
			$sql = '
                SELECT `id`
                FROM `campaign`
            ';
			
			if ($user_id !== null) {
				$sql .= ' WHERE `user_id` = :user_id';
			} else {
				$sql .= ' ORDER BY `id` DESC';
			}
			
			if ($limit !== null) {
				$sql .= ' LIMIT :limit';
			}
			
			$sth = self::$pdo->prepare($sql);
			
			if ($limit !== null) {
				$sth->bindValue(':limit', $limit, PDO::PARAM_INT);
			}
			if ($user_id !== null) {
				$sth->bindValue(':user_id', $user_id);
			}
			
			$sth->execute();
			
			return $sth->fetchAll(PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			throw new TelegramException($e->getMessage());
		}
	}
	
	public static function insertCampaign(
		$user_id,
		$is_follower = 0,
		$twitter_link = null,
		$has_retweet = 0,
		$ethereum_address = null,
		$has_tokens_earned = 0,
		$tokens_earned_count = 0
	) {
		if ($user_id === null) {
			throw new TelegramException('$user_id is null. Can not create campaign');
		}
		
		if (!self::isDbConnected()) {
			return false;
		}
		
		try {
			$sth = self::$pdo->prepare('
                INSERT IGNORE INTO `campaign`
                (`user_id`, `is_follower`, `twitter_link`, `has_retweet`, `ethereum_address`, `has_tokens_earned`, `tokens_earned_count`, `created_at`, `updated_at`)
                VALUES
                (:user_id, :is_follower, :twitter_link, :has_retweet, :ethereum_address, :has_tokens_earned, :tokens_earned_count, :created_at, :updated_at)
            ');
			
			$sth->bindValue(':user_id', $user_id);
			$sth->bindValue(':is_follower', $is_follower);
			$sth->bindValue(':twitter_link', $twitter_link);
			$sth->bindValue(':has_retweet', $has_retweet);
			$sth->bindValue(':ethereum_address', $ethereum_address);
			$sth->bindValue(':has_tokens_earned', $has_tokens_earned);
			$sth->bindValue(':tokens_earned_count', $tokens_earned_count);
			$sth->bindValue(':created_at', self::getTimestamp());
			$sth->bindValue(':updated_at', self::getTimestamp());
			
			return $sth->execute();
		} catch (PDOException $e) {
			throw new TelegramException($e->getMessage());
		}
	}
}