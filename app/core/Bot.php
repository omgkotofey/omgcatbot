<?php


namespace app\core;

use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Telegram;
use Longman\TelegramBot\TelegramLog;
use \Exception;

/**
 * Class Bot - main bot application facade
 *
 * @package app\core
 */
class Bot
{
	/**
	 * @var
	 */
	private static $_app;
	/**
	 * @var Config
	 */
	public $config;
	/**
	 * @var $telegramApiClient Telegram
	 */
	private $telegramApiClient;
	
	/**
	 * Bot constructor.
	 *
	 * @param array $config
	 */
	private function __construct(array $config)
	{
		$this->config = new Config($config);
		$this->init();
	}
	
	private function init()
	{
		try {
			$this->telegramApiClient = new Telegram($this->config->get('bot_token'), $this->config->get('bot_username'));
			$this->telegramApiClient->addCommandsPaths($this->config->get('commands_paths'));
			$this->telegramApiClient->enableAdmins($this->config->get('bot_admins'));
			$this->telegramApiClient->enableMySql($this->config->get('db'));
			
			if ($this->config->get('enable_logs')){
				TelegramLog::initErrorLog("{$this->config->get('logs_path')}/{$this->config->get('bot_username')}_error.log");
				TelegramLog::initDebugLog("{$this->config->get('logs_path')}/{$this->config->get('bot_username')}_debug.log");
				TelegramLog::initUpdateLog("{$this->config->get('logs_path')}/{$this->config->get('bot_username')}_update.log");
			}
		} catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	
	public function run()
	{
		$this->telegramApiClient->enableLimiter();
		$this->telegramApiClient->handle();
	}
	
	public function setWebHook()
	{
		try {
			$result = $this->telegramApiClient->setWebhook($this->config->get('webhook_url'));
			if ($result->isOk()) {
				echo $result->getDescription();
			}
		} catch (TelegramException $e) {
			 echo $e->getMessage();
		}
	}
	
	public function unsetWebHook()
	{
		try {
			$result = $this->telegramApiClient->deleteWebhook();
			if ($result->isOk()) {
				echo $result->getDescription();
			}
		} catch (TelegramException $e) {
			echo $e->getMessage();
		}
	}
	
	/**
	 * @param $config
	 *
	 * @return Bot
	 */
	public static function create($config)
	{
		return self::$_app===null ? self::$_app = new self($config) : self::$_app;
	}
	
	/**
	 * @return Bot
	 */
	public static function app()
	{
		return self::$_app;
	}
}