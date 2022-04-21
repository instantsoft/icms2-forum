<?php if(!$this->controller->request->isAjax()){

$this->setPageTitle(LANG_FORUM_EDIT_POST);

$this->addBreadcrumb(LANG_FORUM_EDIT_POST); ?>

<h1><?php echo LANG_FORUM_EDIT_POST; ?></h1>

<?php } ?>

<?php
$this->renderForm($form, $post, [
    'action' => href_to('forum', 'post_edit', [$post['id']]),
    'method' => 'post',
    'submit' => ['title' => LANG_SEND],
    'cancel' => [
        'show' => true,
        'href' => href_to('forum', $category['slug']),
    ]
], $errors);
