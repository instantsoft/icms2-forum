<?php

class onForumFrontpageTypes extends cmsAction {

    public function run(){

        return [
            'name' => $this->name,
            'types' => [
                'forum:index' => LANG_FORUM_FORUMS
            ]
        ];
    }

}
