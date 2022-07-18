<?php

$is_mobile = (bool) cmsRequest::getDeviceType() === 'mobile';

$this->addTplJSNameFromContext('forum');

$this->addHead('<link rel="canonical" href="'.href_to_abs('forum', $thread['slug'] . '.html').($page > 1 ? '?page='.$page : '').'"/>');

if ($thread_access->is_can_write) {
    $this->addToolButton([
        'class' => 'add',
        'icon'  => 'plus-circle',
        'title' => LANG_FORUM_NEW_MESSAGE,
        'href'  => href_to('forum', 'post_add', $thread['id'])
    ]);
}
if ($thread_access->is_can_fixed && !empty($thread['is_fixed'])) {
    $this->addToolButton([
        'class' => 'cancel',
        'icon'  => 'handshake-slash',
        'title' => LANG_FORUM_UNFIXED,
        'href'  => href_to('forum', 'thread', ['unfixed', $thread['id']]).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($is_moder && empty($thread['is_pinned'])) {
    $this->addToolButton([
        'class' => 'pinthread',
        'icon'  => 'thumbtack',
        'title' => LANG_FORUM_PIN,
        'href'  => href_to('forum', 'thread', ['pin', $thread['id']]).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($is_moder && !empty($thread['is_pinned'])) {
    $this->addToolButton([
        'class' => 'unpinthread',
        'icon'  => 'map-pin',
        'title' => LANG_FORUM_UNPIN,
        'href'  => href_to('forum', 'thread', ['unpin', $thread['id']]).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($thread_access->is_can_send_invite) {
    $this->addToolButton([
        'class' => 'bell',
        'icon'  => 'bell',
        'title' => LANG_FORUM_THREAD_INVITE,
        'href'  => href_to('forum', 'thread_invite', [$thread['id']])
    ]);
}
if ($thread_access->is_can_thread_edit) {
    $this->addToolButton([
        'class' => 'edit',
        'icon'  => 'edit',
        'title' => LANG_FORUM_THREAD_EDIT,
        'href'  => href_to('forum', 'thread_edit', [$thread['id']])
    ]);
}
if ($thread_access->is_can_thread_vip) {
    $this->addToolButton([
        'class' => 'ajax-modal '.(empty($thread['is_vip']) ? 'vip-add' : 'vip-delete'),
        'icon'  => empty($thread['is_vip']) ? 'charging-station' : 'car-battery',
        'title' => empty($thread['is_vip']) ? LANG_FORUM_THREAD_VIP_ADD : LANG_FORUM_THREAD_VIP_CHANGE,
        'href'  => href_to('forum', 'thread_vip', $thread['id']),
    ]);
}
if ($thread_access->is_can_delete) {
    $this->addToolButton([
        'class'   => 'delete',
        'icon'    => 'minus-square',
        'title'   => LANG_FORUM_THREAD_DELETE,
        'confirm' => sprintf(LANG_FORUM_THREAD_DELETE_CONFIRM, $thread['title']),
        'href'    => href_to('forum', 'thread_delete', $thread['id']).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($user->is_admin && !empty($thread['is_deleted'])) {
    $this->addToolButton([
        'class'   => 'restore',
        'icon'    => 'trash-restore-alt',
        'confirm' => LANG_FORUM_THREAD_RESTORE_CONFIRM,
        'title'   => LANG_FORUM_THREAD_RESTORE,
        'href'    => href_to('forum', 'thread_restore', $thread['id']).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($thread_access->is_can_closed) {
    $this->addToolButton([
        'class' => 'lock_open',
        'icon'  => 'lock',
        'title' => LANG_CLOSE,
        'href'  => href_to('forum', 'thread', ['close', $thread['id']]).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}
if ($thread_access->is_can_open) {
    $this->addToolButton([
        'class' => 'lock',
        'icon'  => 'lock-open',
        'title' => LANG_FORUM_OPEN,
        'href'  => href_to('forum', 'thread', ['open', $thread['id']]).'?csrf_token='.cmsForm::getCSRFToken()
    ]);
}

$html_pagebar = html_pagebar($page, $perpage, $total, href_to('forum', $thread['slug'] . '.html'));
?>

<?php ob_start(); ?>
    <h1><?php html($thread['title']); ?></h1>
<?php $this->addToBlock('before_body', ob_get_clean(), true); ?>

<?php if (!empty($thread['badges'])) { ?>
    <div class="mt-n4 mb-3">
        <?php foreach ($thread['badges'] as $type => $badge) { ?>
            <span class="badge badge-<?php echo $type; ?>"><?php echo $badge; ?></span>
        <?php } ?>
    </div>
<?php } ?>

<?php if (!empty($thread['description'])) { ?>
    <div class="thread-description<?php if (!empty($thread['badges'])) { ?><?php } else { ?> mb-2<?php } ?>">
        <?php html($thread['description']); ?>
    </div>
<?php } ?>

<?php if (!empty($thread['from_cat']) && $is_moder) { ?>
    <div class="thread-from-category mb-2">
        <?php echo LANG_FORUM_THREAD_FROM_CAT; ?> <?php echo html_link($thread['from_cat']['title'], href_to('forum', $thread['from_cat']['slug'])); ?>
    </div>
<?php } ?>

<?php if (!empty($options['thread_prepend_html'])) { ?>
    <div class="thread-prepend-html mb-2">
        <?php echo $options['thread_prepend_html']; ?>
    </div>
<?php } ?>

<?php if ($thread_poll) { ?>

    <?php $this->renderChild('thread_poll', array(
        'thread_poll'   => $thread_poll,
        'thread'        => $thread,
        'thread_access' => $thread_access,
        'show_result'   => $thread_poll['first_show_result']
    )); ?>

<?php } ?>

<?php echo $html_pagebar; ?>

<?php $this->renderChild($tpl_posts, array(
    'posts'         => $posts,
    'num'           => $num,
    'thread'        => $thread,
    'users_groups'  => $users_groups,
    'user_avatar_size' => $user_avatar_size,
    'page'          => $page,
    'options'       => $options
)); ?>

<?php echo $html_pagebar; ?>

<?php if (!empty($thread['prev_thread']) || !empty($thread['next_thread'])) { ?>

    <table class="thread-navbar" cellspacing="0" cellpadding="0">

        <tr>

            <?php if (!empty($thread['prev_thread'])) { ?>

                <td width="49%">
                    <div class="thread-navbar-prev">
                        &larr; <a href="<?php echo href_to('forum', $thread['prev_thread']['slug'] . '.html'); ?>" title="<?php echo LANG_FORUM_PREVIOUS_THREAD; ?>">
                            <?php html(string_short($thread['prev_thread']['title'], 30)); ?>
                        </a>
                    </div>
                </td>

            <?php } ?>

            <?php if ($is_mobile) { ?></tr><tr><?php } else { ?>

                <?php if (!empty($thread['prev_thread']) && !empty($thread['next_thread'])) { ?><td width="10">|</td><?php } ?>

            <?php } ?>

            <?php if (!empty($thread['next_thread'])) { ?>

                <td>
                    <div class="thread-navbar-next">
                        <a href="<?php echo href_to('forum', $thread['next_thread']['slug'] . '.html'); ?>" title="<?php echo LANG_FORUM_NEXT_THREAD; ?>">
                            <?php html(string_short($thread['next_thread']['title'], 30)); ?>
                        </a> &rarr;
                    </div>
                </td>

            <?php } ?>

        </tr>

    </table>

<?php } ?>

<?php if (!$thread['is_closed']) { ?>

    <div class="thread-fast-edit">

        <?php if ($options['fast_answer'] && $thread_access->is_can_write && !empty($form)) { ?>

            <div class="thread-fast-edit-header"><?php echo LANG_FORUM_FAST_ANSWER; ?></div>

        <?php $this->renderForm($form, false, [
                'action'  => href_to('forum', 'post_add', [$thread['id']]),
                'method'  => 'post',
                'toolbar' => false,
                'submit'  => ['title' => LANG_SEND]
            ]); ?>

        <?php } elseif ($thread_access->is_can_write) { ?>

            <div class="thread-fast-edit-header">
                <a class="add_post btn btn-outline-primary" href="<?php echo href_to('forum', 'post_add', $thread['id']); ?>"><?php echo LANG_FORUM_NEW_MESSAGE; ?></a>
            </div>

    <?php } else { ?>

            <div class="alert alert-info m-0">
                <?php echo LANG_FORUM_NOT_WRITE_ON_THIS_THREAD; ?>
                <?php if (!$user->is_logged) { ?>
                <br /><?php echo sprintf(LANG_FORUM_FOR_WRITE_ON_FORUM, href_to('auth', 'login'), href_to('auth', 'register')); ?>
                <?php } ?>
            </div>

    <?php } ?>

    </div>

<?php } ?>

<?php if (!empty($options['thread_append_html'])) { ?>
    <div class="thread-append-html"><?php echo $options['thread_append_html']; ?></div>
<?php } ?>

<?php if ($user->is_logged) { ob_start(); ?>
    <script>
        <?php echo $this->getLangJS('LANG_FORUM_ADD_QUOTE_TEXT', 'LANG_FORUM_SELECT_TEXT_QUOTE'); ?>
        <?php if (!$thread['is_closed'] && $options['fast_answer'] && $thread_access->is_can_write) { ?>
            icms.forum.enable_quote = true;
        <?php } ?>
    </script>
<?php $this->addBottom(ob_get_clean()); } ?>
