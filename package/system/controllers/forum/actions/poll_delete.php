<?php

class actionForumPollDelete extends cmsAction {

    public function run($thread_id) {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!$csrf_token || !cmsForm::validateCSRFToken($csrf_token)) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_BAD_CSRF_TOKEN]);
        }

        // Получаем данные темы
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и к теме
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        if (!$this->cat_access->is_moder && !$this->thread_access->is_author) {
            return cmsCore::error404();
        }

        // Получаем данные по опросу
        $poll = $this->model->getThreadPoll($thread['id'], $this->cms_user);

        // Если опрос не найден - выходим
        if (!$poll) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_POLL_IS_DELETE]);
        }

        // Удаляем опрос
        $this->model->deletePoll($poll['id']);

        return $this->cms_template->renderJSON(['error' => false, 'text' => LANG_FORUM_POLL_IS_DELETE]);
    }

}
