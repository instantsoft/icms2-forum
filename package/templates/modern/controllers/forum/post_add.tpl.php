<?php

$this->setPageTitle($thread['title']);

$this->addTplJSNameFromContext('forum');

$this->addBreadcrumb($parent_post ? LANG_FORUM_ADD_REPLY_POST : LANG_FORUM_ADD_NEW_POST);

?>

<h1><?php echo $parent_post ? LANG_FORUM_ADD_REPLY_POST : LANG_FORUM_ADD_NEW_POST; ?></h1>

<?php $this->renderForm($form, $post, array(
    'action'      => '',
    'method'      => 'post',
    'toolbar'     => false,
    'submit'      => array('title' => LANG_SEND),
    'cancel' => array(
        'show' => true,
        'href' => !empty($parent_post) ? href_to('forum', 'pfind', $parent_post['id']) : href_to('forum', $thread['slug'] . '.html'),
    )
), $errors); ?>