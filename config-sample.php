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
		000000000
	],
	/**
	 * Webhook url. Not needed if you will use any of getUpdates() method
	 */
	'webhook_url' => 'https://example.com/',
	/**
	 * Link which user need to follow to start campaign
	 */
	'link_to_follow' => 'https://t.me/example_chanel',
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
	'enable_logs' => false
];