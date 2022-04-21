<?php

class actionForumPost extends cmsAction {

    public function run($action, $post_id) {

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Список доступных действий с сообщением
        if (!in_array($action, ['hide', 'view', 'pin', 'unpin'])) {
            return cmsCore::error404();
        }

        // Получаем данные сообщения
        $post = $this->model->getPost($post_id);
        if (!$post) {
            return cmsCore::error404();
        }

        // Получаем данные по теме сообщения
        $thread = $this->model->getThreadByField($post['thread_id']);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы и сообщения, доступны только администраторам сайта
        if (!$this->cms_user->is_admin && (!empty($post['is_deleted']) || !empty($thread['is_deleted']))) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        $this->loadCatAccess($category['path']);

        // Эти действия доступны только модераторам
        if (!$this->cat_access->is_moder) {
            return cmsCore::error404();
        }

        $change = [];

        // Прикрепить сообщение
        if ($action == 'pin' && !$post['is_pinned']) {

            $change = ['is_pinned' => 1];

            cmsUser::addSessionMessage(LANG_FORUM_POST_IS_PINNED, 'success');
        }

        // Открепить сообщение
        if ($action == 'unpin' && $post['is_pinned']) {

            $change = ['is_pinned' => null];

            cmsUser::addSessionMessage(LANG_FORUM_POST_IS_UNPINNED, 'success');
        }

        // Скрыть сообщение
        if ($action == 'hide' && !$post['is_hidden']) {

            $change = ['is_hidden' => 1];

            cmsUser::addSessionMessage(LANG_FORUM_POST_IS_HIDDEN, 'success');
        }

        // Показать скрытое сообщение
        if ($action == 'view' && $post['is_hidden']) {

            $change = ['is_hidden' => null];

            cmsUser::addSessionMessage(LANG_FORUM_POST_IS_VIEW, 'success');
        }

        // Применение действия
        if ($change) {
            $this->model->updatePost($post['id'], $change);
        } else {
            return cmsCore::error404();
        }

        // Возврат к сообщению
        return $this->runExternalAction('pfind', [$post['id']]);
    }

}
