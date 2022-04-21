<?php

function install_package(){

    $model = new cmsModel();

    if (!$model->db->isFieldExists('{users}', 'forum_sign')) {
        $model->db->query("ALTER TABLE `{users}` ADD `forum_sign` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Подпись на форуме' AFTER `status_text`;");
    }

    if (!$model->db->isFieldExists('{users}', 'forum_posts_count')) {
        $model->db->query("ALTER TABLE `{users}` ADD `forum_posts_count` INT(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Количество сообщений на форуме' AFTER `status_text`;");
    }

    return true;

}

function after_install_package(){

    $model = new cmsModel();

    $model->db->query("UPDATE `{#}controllers` SET `options` = '---\r\ncan_moder: [ ]\r\ncats_level_view: 3\r\ntpl_index: index\r\nperpage_threads: 15\r\nis_rss: 1\r\npreview_thread: null\r\ntpl_cats: category_view\r\nthreads_sorting:\r\n - \r\n by: is_pinned\r\n to: desc\r\n - \r\n by: date_pub\r\n to: desc\r\nmenu_index:\r\n seo:\r\n h1:\r\n title:\r\n desc:\r\n keys:\r\nmenu_lthr:\r\n seo:\r\n h1:\r\n title:\r\n desc:\r\n keys:\r\nmenu_lp:\r\n seo:\r\n h1:\r\n title:\r\n desc:\r\n keys:\r\nperpage_posts: 15\r\nshow_rating: 1\r\npoll_counts: 12\r\nitem_url_pattern: \'{title}\'\r\nthreads:\r\n seo_title_pattern:\r\n seo_keys_pattern: \'{title|string_get_meta_keywords}\'\r\n seo_desc_pattern: \'{title|string_get_meta_description}\'\r\ntpl_threads: thread_view\r\nthread_prepend_html:\r\nthread_append_html:\r\nthread_enable_subscriptions: 1\r\nthread_subscriptions_letter_tpl:\r\nthread_subscriptions_notify_text:\r\nuser_fields: null\r\npost_interval: 20\r\nfast_answer: 1\r\ncombine_post: null\r\ncombine_interval: 1440\r\nquote_template: |\r\n <blockquote>\r\n <p>{content}</p>\r\n <footer class=\"blockquote-footer\">\r\n <cite>{user_nickname}</cite>\r\n </footer>\r\n </blockquote>\r\n &nbsp;\r\neditor: 3\r\neditor_presets: null\r\nis_html_filter: 1\r\ntpl_posts: posts_view\r\nenable_file: null\r\nfile_ext: >\r\n txt, doc, zip, rar, arj, png, gif, jpg,\r\n jpeg\r\nfile_max_size: 10\r\nseo_title: Форумы\r\nseo_h1: Форумы\r\nseo_keys:\r\nseo_desc:\r\nfix_threads_reads: null\r\nshow_ds_menu_index: null\r\nshow_ds_menu_lthr: null\r\nshow_ds_menu_lp: null\r\nshow_ds_menu_mythr: null\r\nshow_ds_menu_myp: null\r\nshow_users_groups: null\r\nbuild_redirect_link: null \r\n' WHERE `name` = 'forum'");

    return true;

}
