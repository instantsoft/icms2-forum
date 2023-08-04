<?php $this->renderForm($form, $post, [
    'action' => href_to('forum', 'post_move', [$post['id']]),
    'method' => 'ajax',
    'toolbar' => false,
    'submit'  => ['title' => LANG_SEND]
], $errors); ?>