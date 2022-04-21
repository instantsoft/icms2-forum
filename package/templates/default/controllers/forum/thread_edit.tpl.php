<?php $this->renderForm($form, $thread, [
    'action'  => href_to('forum', 'thread_edit', $thread['id']),
    'method'  => 'ajax',
    'toolbar' => false
], $errors); ?>
