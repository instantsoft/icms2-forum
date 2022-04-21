<?php
/**
 * Удаление темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThreadDelete extends cmsAction {

    public function run($thread_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
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

        if (!$this->thread_access->is_can_delete) {
            return cmsCore::error404();
        }

        // Удаляем тему
        $this->model->deleteThread($thread, $this->cms_user->is_admin);

        // Обновляем количество и последнее сообщение в родительских разделах
        $this->model->updateLastPostAfterThreadEdit($category);

        // Сообщаем пользователю об успешности операции
        cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_DELETED, 'success');

        $this->redirectTo('forum', $category['slug']);
    }

}
