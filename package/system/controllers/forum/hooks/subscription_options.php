<?php

class onForumSubscriptionOptions extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subject){

        return [
            'letter_tpl'  => !empty($this->options['thread_subscriptions_letter_tpl']) ? $this->options['thread_subscriptions_letter_tpl'] : '',
            'notify_text' => !empty($this->options['thread_subscriptions_notify_text']) ? $this->options['thread_subscriptions_notify_text'] : ''
        ];
    }

}
