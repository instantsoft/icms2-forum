<?php
/**
 * Удаление поста
 *
 * @property \forum $cat_access
 * @property \modelForum $model
 */
class actionForumPostDelete extends cmsAction {

    public function run($post_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        // Получаем данные сообщения
        $post = $this->model->getPost($post_id);
        if (!$post || $post['is_first']) {
            return cmsCore::error404();
        }

        // Получаем данные темы
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
        if (!$category || (!$category['is_pub'] && !$this->cms_user->is_admin)) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        $this->loadCatAccess($category['path']);

        if (!$this->cat_access->is_moder && !$this->isPostCanEdit($post)) {
            return cmsCore::error404();
        }

        // Удаляем само сообщение
        $this->model->deletePost($post, $this->cms_user->is_admin);

        cmsUser::addSessionMessage(LANG_FORUM_POST_IS_DELETED, 'success');

        // Обновляем количество и последнее сообщение в старом форуме
        $this->model->updateLastPostAfterPostEdit($thread);

        // Обновляем количество и последнее сообщение родительских разделах
        $this->model->updateLastPostAfterThreadEdit($category);

        // Если не администратор, скрываем удаленные сообщения и темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }
        // Определяем предыдущее сообщение в этой теме
        $prev_post = $this->model->
                filterEqual('thread_id', $thread['id'])->
                filterLt('id', $post['id'])->
                orderBy('date_pub', 'desc')->
                getItem('forum_posts');

        return $this->runExternalAction('pfind', [$prev_post['id']]);
    }

}
