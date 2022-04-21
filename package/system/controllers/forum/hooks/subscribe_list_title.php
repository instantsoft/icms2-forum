<?php

class onForumSubscribeListTitle extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target, $subscribe) {

        $list_title = LANG_FORUM_CONTROLLER;

        // Если нет данных страницы, возвращаем название "Форум"
        if (empty($target['params']['filters'][0]['value']) || !is_numeric($target['params']['filters'][0]['value'])) {
            return $list_title;
        }

        // Если тема форума, возвращаем ее название
        if ($target['params']['filters'][0]['field'] === 'thread_id') {

            $thread = $this->model->getThreadByField($target['params']['filters'][0]['value']);

            return !empty($thread['title']) ? $thread['title'] : $list_title;
        }

        return $list_title;
    }

}
