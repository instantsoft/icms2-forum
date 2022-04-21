<?php

$page_title = $do == 'add' ?
        LANG_CP_FORUM_CAT_CREATE :
        LANG_FORUM_CAT_EDIT . ': ' . $category['title'];

$this->setPageTitle($do == 'add' ? LANG_CP_FORUM_CAT_CREATE : LANG_FORUM_CAT_EDIT);

$this->addBreadcrumb($do == 'add' ? LANG_CP_FORUM_CAT_CREATE : LANG_FORUM_CAT_EDIT);

$this->addToolButton(array(
    'class' => 'save',
    'title' => LANG_SAVE,
    'href'  => "javascript:icms.forms.submit()"
));

$this->addToolButton(array(
    'class' => 'cancel',
    'title' => LANG_CANCEL,
    'href'  => $this->href_to('')
));

$this->renderForm($form, $category, array(
    'action' => '',
    'method' => 'post'
), $errors);
