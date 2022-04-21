<?php

class onForumCronVipexpires extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $this->model->filterNotNull('is_vip')->
                filterIsNull('is_deleted')->
                filter('i.vip_expires <= NOW()')->
                get('forum_threads', function ($item, $model) {

                    $model->updateThread($item['id'], ['is_vip' => null, 'vip_expires' => null]);

                    return $item['id'];
                });

        return true;
    }

}
