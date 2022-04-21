<?php

class onForumUserPrivacyTypes extends cmsAction {

    public function run() {

        $types['view_user_forum_posts'] = [
            'title'   => sprintf(LANG_USERS_PRIVACY_PROFILE_CTYPE, LANG_FORUM_FORUMS_POSTS),
            'options' => ['anyone', 'friends']
        ];

        return $types;
    }

}
