<?php if ($posts) { ?>

    <div id="forum_posts_list" class="forum-posts-list striped-list">

    <?php $this->renderChild($options['tpl_posts'], [
        'posts'            => $posts,
        'num'              => 1,
        'thread'           => [],
        'is_moder'         => false,
        'users_groups'     => $users_groups,
        'user_avatar_size' => $user_avatar_size,
        'page'             => $page,
        'options'          => $options
    ]); ?>

    </div>

    <?php if ($perpage < $total) { ?>
        <?php echo html_pagebar($page, $perpage, $total, $page_url); ?>
    <?php } ?>

<?php } else {
    echo LANG_FORUM_NOT_POSTS;
} ?>
