<?php

$this->setPageTitle(empty($thread['is_vip']) ? LANG_FORUM_THREAD_VIP_ADD : LANG_FORUM_THREAD_VIP_CHANGE);
$this->setPageDescription(empty($thread['is_vip']) ? LANG_FORUM_THREAD_VIP_ADD : LANG_FORUM_THREAD_VIP_CHANGE);
$this->setPageKeywords(empty($thread['is_vip']) ? LANG_FORUM_THREAD_VIP_ADD : LANG_FORUM_THREAD_VIP_CHANGE);

?>

<h1><?php echo empty($thread['is_vip']) ? LANG_FORUM_THREAD_VIP_ADD : LANG_FORUM_THREAD_VIP_CHANGE; ?></h1>

<?php

$this->renderForm($form, $thread, array(
    'action' => '',
    'method' => 'post',
    'toolbar' => false,
), $errors);
