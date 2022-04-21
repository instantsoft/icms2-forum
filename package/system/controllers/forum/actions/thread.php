<?php
/**
 * Действия с темой
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThread extends cmsAction {

    public function run($action, $thread_id) {

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Список доступных действий с темой
        if (!in_array($action, ['open', 'close', 'pin', 'unpin', 'unfixed'])) {
            return cmsCore::error404();
        }

        // Получаем данные по теме форума
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

        // Загружаем доступ к разделу и теме
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        $change = [];

        // Закрытие темы
        if ($action == 'close') {

            if (!$this->thread_access->is_can_closed) {
                return cmsCore::error404();
            }

            $change = ['is_closed' => 1];

            cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_CLOSE, 'success');
        }

        // Снятие метки "РЕШЕНО" у темы
        if ($action == 'unfixed' && $thread['is_fixed']) {

            if (!$this->thread_access->is_can_fixed) {
                return cmsCore::error404();
            }

            $change = ['is_fixed' => 0];

            cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_NOT_FIXED, 'success');
        }

        // Открытие темы
        if ($action == 'open') {

            if (!$this->thread_access->is_can_open) {
                return cmsCore::error404();
            }

            $change = ['is_closed' => 0];

            cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_OPEN, 'success');
        }

        // Только модератор может закреплять или откреплять тему
        if ($this->cat_access->is_moder) {

            // Закрепление темы
            if ($action == 'pin' && !$thread['is_pinned']) {

                $change = ['is_pinned' => 1];

                cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_PINNED, 'success');
            }

            // Открепление темы
            if ($action == 'unpin' && $thread['is_pinned']) {

                $change = ['is_pinned' => 0];

                cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_UNPINNED, 'success');
            }
        }

        // Применение изменений
        if ($change) {
            $this->model->updateThread($thread['id'], $change);
        } else {
            return cmsCore::error404();
        }

        $this->redirectBack();
    }

}
