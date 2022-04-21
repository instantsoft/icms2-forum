<?php

class onForumCronAutoflood extends cmsAction {

    public $disallow_event_db_register = true;

    public function run() {

        $posts = $this->model->
                select('t.category_id', 'category_id')->
                filterIsNull('is_first')->
                filterIsNull('is_deleted')->
                filter('i.flood_time <= NOW()')->
                joinLeft('forum_threads', 't', 't.id = i.thread_id')->
                get('forum_posts', function ($item, $model) {

            $item['files'] = $model->yamlToArray($item['files']);

            $model->deletePost($item);

            return [
                'id'          => $item['id'],
                'thread_id'   => $item['thread_id'],
                'category_id' => $item['category_id'],
                'category'    => $model->getCategoryByField($item['category_id']),
            ];
        });

        if ($posts) {

            foreach ($posts as $post) {

                // обновляем количество и последнее сообщение форуме
                $this->model->updateLastPostAfterPostEdit(['id' => $post['thread_id']]);

                // обновляем количество и последнее сообщение в родительских разделах
                $this->model->updateLastPostAfterThreadEdit($post['category']);
            }
        }

        return true;
    }

}
