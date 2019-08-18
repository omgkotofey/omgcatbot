<?php

return [
	/**
	 * Bot's API token
	 */
	'bot_token' => '111111111:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA',
	/**
	 * Bot's username without @ symbol
	 */
	'bot_username' => 'example_bot',
	/**
	 * Array of users which will have admin access to bot's private chat
	 */
	'bot_admins' =>[
		000000000,
		123456789
	],
	/**
	 * Webhook url. Not needed if you will use any of getUpdates() method
	 */
	'webhook_url' => 'https://example.com/',
	/**
	 * MySQL database credentials. Always necessary.
	 */
	'db' => [
		'host'     => 'localhost',
		'port'     => 3306,
		'user'     => 'example_user',
		'password' => 'example_password',
		'database' => 'example_database',
	],
	/**
	 * Enable or disable logs (/app/logs dir needs to be at least 755 accesses)
	 */
	'enable_logs' => false,
	/**
	 * All campaign stuff
	 *
	 * All positions in this section is optional, you can add your own if your campaign path is another
	 * The only thing you need - to change \app\commands\GenericmessageCommand class
	 * This campaign stuff and GenericmessageCommand class is only example ones. Your campaign can be different.
	 *
	 *
	 * Telegram group which user need to join after campaign start
	 */
	'telegram_group_to_follow_id' => '-111111111',
	'telegram_group_to_follow_link_url' => 'https://t.me/joinchat/123ABC123ABC',
	/**
	 * Telegram channel which user need to join after campaign start
	 */
	'telegram_channel_to_follow_id' => '-2222222222222',
	'telegram_channel_to_follow_link_url' => 'https://t.me/example_channel',
	/**
	 * Link to twitter profile which lase tweet user must to retweet
	 */
	'twitter_profile_url' => 'https://twitter.com/example_profile',
	/**
	 * Link to support contacts
	 */
	'support_link' => '@example_group',
];