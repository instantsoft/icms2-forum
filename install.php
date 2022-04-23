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

    $model->db->query("UPDATE `{#}controllers` SET `options` = '---\ncan_moder: [ ]\ncats_level_view: 3\ntpl_index: index\nperpage_threads: 15\nis_rss: 1\npreview_thread: null\ntpl_cats: category_view\nthreads_sorting:\n  - \n    by: is_pinned\n    to: desc\n  - \n    by: date_pub\n    to: desc\nmenu_index:\n  seo:\n    h1:\n    title:\n    desc:\n    keys:\nmenu_lthr:\n  seo:\n    h1:\n    title:\n    desc:\n    keys:\nmenu_lp:\n  seo:\n    h1:\n    title:\n    desc:\n    keys:\nperpage_posts: 15\nshow_rating: 1\npoll_counts: 12\nitem_url_pattern: \'{title}\'\nthreads:\n  seo_title_pattern:\n  seo_keys_pattern: \'{title|string_get_meta_keywords}\'\n  seo_desc_pattern: \'{title|string_get_meta_description}\'\ntpl_threads: thread_view\nthread_prepend_html:\nthread_append_html:\nthread_enable_subscriptions: 1\nthread_subscriptions_letter_tpl:\nthread_subscriptions_notify_text:\nuser_fields: null\npost_interval: 20\nfast_answer: 1\ncombine_post: null\ncombine_interval: 1440\nquote_template: |\n  <blockquote>\r\n  <p>{content}</p>\r\n  <footer class=\"blockquote-footer\">\r\n  <cite>{user_nickname}</cite>\r\n  </footer>\r\n  </blockquote>\r\n  &nbsp;\neditor: 3\neditor_presets: null\nis_html_filter: 1\ntpl_posts: posts_view\nenable_file: null\nfile_ext: >\n  txt, doc, zip, rar, arj, png, gif, jpg,\n  jpeg\nfile_max_size: 10\nseo_title: Форумы\nseo_h1: Форумы\nseo_keys:\nseo_desc:\nfix_threads_reads: null\nshow_ds_menu_index: null\nshow_ds_menu_lthr: null\nshow_ds_menu_lp: null\nshow_ds_menu_mythr: null\nshow_ds_menu_myp: null\nshow_users_groups: null\nbuild_redirect_link: null\n' WHERE `name` = 'forum'");

    return true;

}
