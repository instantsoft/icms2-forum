<?php

class onForumComplaintsUrl extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subject, $post_id){

        if ($subject !== 'post') { return false; }

        $post = $this->model->getPost($post_id);
        if (!$post) { return false; }

        if ($post['user_id'] == $this->cms_user->id) { return false; }

        $thread = $this->model->getThreadByField($post['thread_id']);
        if (!$thread) { return false; }

        return [
            'title' => $thread['title'],
            'title_subj' => LANG_FORUM_THREAD_POST,
            'url' => href_to_abs('forum', 'pfind', [$post['id']])
        ];
    }

}
