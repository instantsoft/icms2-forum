<?php
$this->renderForm($form, $thread, [
    'action'  => href_to('forum', 'thread_vip', $thread['id']),
    'method'  => 'post',
    'toolbar' => false,
], $errors);
