<?php

class onForumUserNotifyTypes extends cmsAction {

    public function run() {
        return [
            'forum_invite_thread' => [
                'title' => LANG_FORUM_NOTIFY_THREAD_INVITE
            ]
        ];
    }

}
