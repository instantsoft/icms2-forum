<?php

class onForumAdminSubscriptionsList extends cmsAction {

    public function run($items) {

        if ($items) {

            foreach ($items as $key => $item) {

                if ($item['controller'] !== 'forum') {
                    continue;
                }
                if ($item['subject'] !== 'thread') {
                    continue;
                }

                $items[$key]['subject'] = LANG_FORUM_ADD_POSTS;
            }
        }

        return $items;
    }

}
