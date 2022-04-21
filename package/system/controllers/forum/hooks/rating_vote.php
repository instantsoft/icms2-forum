<?php

class onForumRatingVote extends cmsAction {

    public function run($data) {

        if (empty($data['target']['thread_id'])) {
            return $data;
        }

        $post = $data['target'];

        $thread = $this->model->getThreadByField($post['thread_id']);

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!$thread || (!empty($thread['is_deleted']) && !$this->cms_user->is_admin)) {
            return $data;
        }

        cmsCore::getController('activity')->addEntry('forum', 'vote.post', [
            'subject_title' => $thread['title'],
            'subject_id'    => $post['id'],
            'subject_url'   => href_to_rel('forum', 'pfind', [$post['id']])
        ]);

        return $data;
    }

}
