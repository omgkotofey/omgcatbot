CREATE TABLE IF NOT EXISTS `campaign` (
  `id` bigint COMMENT 'Unique identifier for this campaign',
  `user_id` bigint NOT NULL COMMENT 'Unique identifier for campaign user or bot',
  `is_follower` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'User already followed us on Twitter',
  `twitter_link` TEXT DEFAULT NULL COMMENT 'Received from user Twitter link',
  `has_retweet` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'User''s twitter link is really retweet of owr''s twitter account tweet',
  `ethereum_address` CHAR(50) DEFAULT NULL COMMENT 'User''s ethereum address',
  `has_tokens_earned` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Has user earned any tokens from us',
  `tokens_earned_count` INTEGER NOT NULL DEFAULT 0 COMMENT 'How much tokens user already earned',
  `created_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date creation',
  `updated_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date update',
  `finished_at` timestamp NULL DEFAULT NULL COMMENT 'Entry date finishing',

  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;