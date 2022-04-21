<?php
$this->setPageTitle($page_title);

$this->addTplJSNameFromContext('forum');
?>
<h1><?php echo $page_title; ?></h1>

<?php

$this->renderForm($form, $thread, array(
    'action' => '',
    'method' => 'post',
    'toolbar' => false,
    'submit' => array(
        'title' => $do == 'add' ? LANG_CREATE : LANG_SAVE,
    ),
    'cancel' => array(
        'show' => true,
        'href' => $do == 'add' ? href_to('forum', $category['slug']) : href_to('forum', $thread['slug'] . '.html'),
    )
), $errors);
