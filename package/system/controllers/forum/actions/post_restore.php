<?php

class actionForumPostRestore extends cmsAction {

    public function run($post_id) {

        if (!$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        $post = $this->model->getPost($post_id);
        if (!$post) {
            return cmsCore::error404();
        }

        $this->model->updatePost($post['id'], ['is_deleted' => null]);

        $thread = $this->model->getThreadByField($post['thread_id']);

        if ($thread) {
            // Обновляем количество и последнее сообщение в старом форуме
            $this->model->updateLastPostAfterPostEdit($thread);
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);

        if ($category) {
            // Обновляем количество и последнее сообщение в родительских разделах
            $this->model->updateLastPostAfterThreadEdit($category);
        }

        cmsEventsManager::hook('forum_post_after_restore', $post);

        return $this->runExternalAction('pfind', [$post['id']]);
    }

}
