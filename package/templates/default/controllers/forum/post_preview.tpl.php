<?php $user = cmsUser::getInstance(); ?>

<div class="posts-table-preview">

    <?php $this->renderChild($tpl_posts, array(
        'posts'         => array(
            array(
                'id'                     => 1,
                'thread_id'              => 1,
                'user_id'                => $user->id,
                'is_pinned'              => 0,
                'is_hidden'              => 0,
                'is_first'               => false,
                'date_pub'               => date('Y-m-d H:i:s'),
                'date_last_modified'     => date('Y-m-d H:i:s'),
                'modified_count'         => 0,
                'from_thread_id'         => false,
                'rating'                 => 0,
                'files'                  => array(),
                'content'                => $content,
                'content_html'           => $content_html,
                'user_nickname'          => $user->nickname,
                'user_avatar'            => $user->avatar,
                'user_groups'            => $user->groups,
                'wday'                   => date('w'),
                'user_city'              => !empty($user->city) ? $user->city : false,
                'user_city_cache'        => !empty($user->city_cache) ? $user->city_cache : false,
                'is_deleted'             => false,
                'flood_time'             => false,
                'is_flood'               => false,
                'user_is_admin'          => $user->is_admin,
                'user_is_locked'         => $user->is_locked,
                'user_karma'             => $user->karma,
                'user_date_log'          => $user->date_log,
                'user_forum_sign'        => $user->forum_sign,
                'user_forum_posts_count' => $user->forum_posts_count,
                'is_online'              => true,
                'is_author'              => true,
                'is_author_can_edit'     => $user->is_admin,
                'rating_widget'          => false
            )
        ),
        'num'           => 1,
        'thread'        => array('id' => 1),
        'is_moder'      => $user->is_admin,
        'is_abuses'     => $is_abuses,
        'is_can_write'  => true,
        'is_can_attach' => $user->is_admin,
        'user_fields'   => array(),
        'users_groups'  => array(),
        'is_mobile'     => $is_mobile,
        'lang_days'     => $lang_days,
        'page'          => 1,
        'options'       => $options
    )); ?>

</div>
