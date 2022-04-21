<?php if ($posts) { ?>

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

<?php echo html_pagebar($page, $perpage, $total, $page_url); ?>

<?php } else { ?>
    <div class="alert alert-info mt-4 alert-list-empty">
        <?php echo LANG_FORUM_NOT_POSTS; ?>
    </div>
<?php } ?>