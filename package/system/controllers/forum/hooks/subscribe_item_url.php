<?php

class onForumSubscribeItemUrl extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target){

        // Ссылка на главную форума
        $list_url = href_to_rel('forum');

        // Если нет данных страницы, возвращаем ссылку на главную форума
        if(empty($target['params']['filters'][0]['value']) || !is_numeric($target['params']['filters'][0]['value'])){
            return $list_url;
        }

        // Если тема форума, возвращаем ссылку на нее
        if($target['params']['filters'][0]['field'] === 'thread_id') {

            $thread = $this->model->getThreadByField($target['params']['filters'][0]['value']);

            return href_to_rel('forum', $thread['slug'] . '.html');
        }

        return $list_url;
    }

}
