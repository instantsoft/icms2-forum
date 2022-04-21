<?php

$this->setPageTitle($thread['title']);
$this->setPageDescription($thread['description']);
$this->setPageKeywords($thread['title']);

$this->addTplJSNameFromContext('forum');

$this->addBreadcrumb($parent_post ? LANG_FORUM_ADD_REPLY_POST : LANG_FORUM_ADD_NEW_POST);

?>

<h1><?php echo $parent_post ? LANG_FORUM_ADD_REPLY_POST : LANG_FORUM_ADD_NEW_POST; ?></h1>

<?php $this->renderForm($form, $post, array(
    'action'      => '',
    'method'      => 'post',
    'toolbar'     => false,
    'submit'      => array('title' => LANG_SEND),
    'buttons' => array(
        array(
            'title' => LANG_PREVIEW,
            'name' => 'preview',
            'onclick' => "icms.forum.previewPost('" . href_to('forum', 'post_preview_ajax') . "');",
            'attributes' => array('class' => 'button-preview btn-secondary')
        )
    ),
    'cancel' => array(
        'show' => true,
        'href' => !empty($parent_post) ? href_to('forum', 'pfind', $parent_post['id']) : href_to('forum', $thread['slug'] . '.html'),
    )
), $errors); ?>

<?php ob_start(); ?>
<script><?php echo $this->getLangJS('LANG_FORUM_ATTACH_FILES'); ?></script>
<?php $this->addBottom(ob_get_clean()); ?>