<?php

class onForumSitemapSources extends cmsAction {

    public function run() {
        return [
            'name' => $this->name,
            'sources' => [
                'cats'    => LANG_FORUM_CONTROLLER . ': ' . LANG_FORUM_CATS,
                'threads' => LANG_FORUM_CONTROLLER . ': ' . LANG_FORUM_THREADS
            ]
        ];
    }

}
