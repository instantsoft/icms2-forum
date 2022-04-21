DELETE FROM `{#}activity_types` WHERE `controller` = 'forum';
INSERT INTO `{#}activity_types` (`is_enabled`, `controller`, `name`, `title`, `description`) VALUES
(0, 'forum', 'add.thread', 'Добавление темы на форуме', 'создаёт тему %s'),
(0, 'forum', 'add.post', 'Добавление сообщений на форуме', 'отвечает в теме %s'),
(0, 'forum', 'vote.post', 'Оценка сообщений на форуме', 'оценивает сообщение %s');

DELETE FROM `{#}perms_rules` WHERE `controller` = 'forum';
INSERT INTO `{#}perms_rules` (`controller`, `name`, `type`, `options`, `show_for_guest_group`) VALUES
('forum', 'attach_add', 'flag', NULL, NULL),
('forum', 'is_moderator', 'flag', NULL, NULL),
('forum', 'poll_add', 'flag', NULL, NULL),
('forum', 'post_add', 'flag', NULL, NULL),
('forum', 'post_add_karma', 'number', NULL, NULL),
('forum', 'post_edit', 'list', 'own, all', NULL),
('forum', 'post_edit_time', 'number', NULL, NULL),
('forum', 'rate', 'flag', NULL, NULL),
('forum', 'send_invite', 'flag', NULL, NULL),
('forum', 'thread_add', 'flag', NULL, NULL),
('forum', 'thread_add_karma', 'number', NULL, NULL),
('forum', 'thread_edit', 'list', 'own, all', NULL),
('forum', 'thread_vip', 'flag', NULL, NULL),
('forum', 'thread_close', 'list', 'own,all', NULL),
('forum', 'thread_open', 'list', 'own,all', NULL),
('forum', 'thread_delete', 'list', 'own,all', NULL);

DELETE FROM `{users}_fields` WHERE `name` = 'forum_sign';
INSERT INTO `{users}_fields` (`name`, `title`, `hint`, `ordering`, `is_enabled`, `fieldset`, `type`, `is_in_list`, `is_in_item`, `is_in_filter`, `is_private`, `is_fixed`, `is_fixed_type`, `is_system`, `values`, `options`, `groups_read`, `groups_add`, `groups_edit`, `filter_view`) VALUES
('forum_sign', 'Подпись к сообщениям на форуме', NULL, 12, 1, 'Анкета', 'string', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '---\nmin_length: 0\nmax_length: 255\nshow_symbol_count: 1\nin_filter_as: input\nteaser_len:\nis_autolink: null\nlabel_in_list: none\nlabel_in_item: top\nis_required: null\nis_digits: null\nis_alphanumeric: null\nis_email: null\nis_url: null\nis_unique: null\nauthor_access: null\n', '---\n', '---\n', '---\n', '---\n');

DELETE FROM `{#}rss_feeds` WHERE `ctype_name` = 'forum';
INSERT INTO `{#}rss_feeds` (`ctype_name`, `title`, `description`, `image`, `mapping`, `limit`, `is_enabled`, `is_cache`, `cache_interval`, `date_cached`, `template`) VALUES
('forum', 'Форумы', 'Новое на форумах', NULL, NULL, '15', '1', NULL, '60', NULL, 'forum');

DELETE FROM `{#}scheduler_tasks` WHERE `controller` = 'forum';
INSERT INTO `{#}scheduler_tasks` (`title`, `controller`, `hook`, `period`, `date_last_run`, `is_active`, `is_new`) VALUES
('Автоудаление сообщений на форуме', 'forum', 'autoflood', 10, NULL, 1, 0),
('Снятие выделения тем на форуме', 'forum', 'vipexpires', '1440', NULL, '1', '1');

DELETE FROM `{users}_tabs` WHERE `name` = 'forum';
INSERT INTO `{users}_tabs` (`title`, `controller`, `name`, `is_active`, `ordering`, `groups_view`, `groups_hide`, `show_only_owner`) VALUES
('Форум', 'forum', 'forum', '1', NULL, NULL, NULL, NULL);

DELETE FROM `{#}widgets_pages` WHERE `controller` = 'forum';
INSERT INTO `{#}widgets_pages` (`controller`, `name`, `title_const`, `title_subject`, `title`, `url_mask`, `url_mask_not`) VALUES
('forum', 'main', 'LANG_WP_FORUM_MAIN_PAGE', 'Форум', NULL, 'forum', NULL),
('forum', 'all', 'LANG_WP_FORUM_ALL_PAGES', 'Форум', NULL, 'forum*', NULL),
('forum', 'list', 'LANG_WP_FORUM_LIST', 'Форум', NULL, 'forum/*', 'forum/*.html*'),
('forum', 'item', 'LANG_WP_FORUM_ITEM', 'Форум', NULL, 'forum/*.html*', NULL);

DELETE FROM `{#}widgets` WHERE `controller` = 'forum';
INSERT INTO `{#}widgets` (`controller`, `name`, `title`, `author`, `url`, `version`, `is_external`, `files`, `addon_id`, `image_hint_path`) VALUES
('forum', 'cats', 'Список форумов', 'InstantCMS Team', 'https://instantcms.ru', '2.0.0', 1, NULL, NULL, NULL),
('forum', 'threads', 'Темы на форуме', 'InstantCMS Team', 'https://instantcms.ru', '2.0.0', 1, NULL, NULL, NULL),
('forum', 'posts', 'Сообщения на форуме', 'InstantCMS Team', 'https://instantcms.ru', '2.0.0', 1, NULL, NULL, NULL),
('forum', 'stats', 'Статистика форума', 'InstantCMS Team', 'https://instantcms.ru', '2.0.0', 1, NULL, NULL, NULL);

DROP TABLE IF EXISTS `{#}forum_cats`;
CREATE TABLE IF NOT EXISTS `{#}forum_cats` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `slug_key` varchar(255) DEFAULT NULL,
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keys` varchar(255) DEFAULT NULL,
  `seo_desc` varchar(255) DEFAULT NULL,
  `is_pub` tinyint(1) UNSIGNED DEFAULT NULL,
  `as_folder` tinyint(1) UNSIGNED DEFAULT NULL,
  `ns_left` int(11) UNSIGNED DEFAULT NULL,
  `ns_right` int(11) UNSIGNED DEFAULT NULL,
  `ns_level` int(11) UNSIGNED DEFAULT NULL,
  `ns_ignore` tinyint(1) UNSIGNED DEFAULT 0,
  `ns_differ` varchar(32) NOT NULL DEFAULT '',
  `ordering` int(11) UNSIGNED DEFAULT NULL,
  `icon` text DEFAULT NULL,
  `threads_count` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `posts_count` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_post` text DEFAULT NULL,
  `moderators` text DEFAULT NULL,
  `options` text DEFAULT NULL,
  `groups_read` text DEFAULT NULL,
  `groups_edit` text DEFAULT NULL,
  `autoflood` tinyint(1) UNSIGNED DEFAULT NULL,
  `date_last_modified` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `ns_left` (`ns_left`,`ns_right`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с категориями форума';

INSERT INTO `{#}forum_cats` (`id`, `parent_id`, `title`, `description`, `slug`, `slug_key`, `seo_title`, `seo_keys`, `seo_desc`, `is_pub`, `as_folder`, `ns_left`, `ns_right`, `ns_level`, `ns_ignore`, `ns_differ`, `ordering`, `icon`, `threads_count`, `posts_count`, `last_post`, `moderators`, `options`, `groups_read`, `groups_edit`, `autoflood`) VALUES
(1, 0, 'Корневой раздел', NULL, 'all', NULL, NULL, NULL, NULL, 1, 1, 1, 2, 0, 0, '', 1, NULL, 0, 0, NULL, NULL, '---\n', '---\n', '---\n', NULL);

DROP TABLE IF EXISTS `{#}forum_polls`;
CREATE TABLE `{#}forum_polls` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `thread_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `options` varchar(250) DEFAULT NULL,
  `answers` text DEFAULT NULL,
  `date_pub_end` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с опросами форумов';

DROP TABLE IF EXISTS `{#}forum_poll_votes`;
CREATE TABLE `{#}forum_poll_votes` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `poll_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `answer_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `date_pub` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `poll_id` (`poll_id`,`user_id`) USING BTREE,
  KEY `date_pub` (`date_pub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Таблица с голосами опросов форумов';

DROP TABLE IF EXISTS `{#}forum_posts`;
CREATE TABLE `{#}forum_posts` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `thread_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `is_pinned` tinyint(1) UNSIGNED DEFAULT NULL,
  `is_hidden` tinyint(1) UNSIGNED DEFAULT NULL,
  `is_first` tinyint(1) DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_last_modified` timestamp NULL DEFAULT NULL,
  `modified_count` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `from_thread_id` int(11) UNSIGNED DEFAULT NULL,
  `rating` int(11) NOT NULL DEFAULT 0,
  `files` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `content_html` text DEFAULT NULL,
  `flood_type` tinyint(1) UNSIGNED DEFAULT NULL,
  `flood_time` datetime DEFAULT NULL,
  `is_deleted` tinyint(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `thread_id` (`thread_id`),
  KEY `user_id` (`user_id`),
  KEY `date_pub` (`date_pub`),
  KEY `rating` (`rating`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Таблица с сообщениями форумов';

DROP TABLE IF EXISTS `{#}forum_threads`;
CREATE TABLE `{#}forum_threads` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `category_id` int(11) UNSIGNED DEFAULT NULL,
  `title` varchar(250) DEFAULT NULL,
  `slug` varchar(250) DEFAULT NULL,
  `description` varchar(250) DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_last_modified` timestamp NULL DEFAULT NULL,
  `fixed_first_post` tinyint(1) UNSIGNED DEFAULT NULL,
  `is_fixed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_closed` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_pinned` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `is_vip` tinyint(1) UNSIGNED NOT NULL DEFAULT 0,
  `vip_expires` datetime DEFAULT NULL,
  `hits` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `from_cat` int(11) UNSIGNED DEFAULT NULL,
  `posts_count` int(11) UNSIGNED NOT NULL DEFAULT 0,
  `last_post` text DEFAULT NULL,
  `is_deleted` tinyint(1) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `category_id` (`category_id`),
  KEY `slug` (`slug`),
  KEY `date_last_modified` (`date_last_modified`),
  KEY `hits` (`hits`),
  KEY `posts_count` (`posts_count`),
  KEY `date_pub` (`date_pub`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Таблица с темами форумов';

DROP TABLE IF EXISTS `{#}forum_threads_hits`;
CREATE TABLE `{#}forum_threads_hits` (
  `thread_id` int(11) UNSIGNED DEFAULT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `date_pub` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  UNIQUE KEY `thread_id` (`thread_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `{#}forum_posts` ADD FULLTEXT KEY `content_html` (`content_html`);
ALTER TABLE `{#}forum_threads` ADD FULLTEXT KEY `title` (`title`,`description`);