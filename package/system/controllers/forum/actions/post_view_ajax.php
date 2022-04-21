<?php
/**
 * Просмотр текста скрытого поста
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumPostViewAjax extends cmsAction {

    public function run($post_id) {

        if (!$this->request->isAjax()) {
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
        if (!$category || !$category['is_pub']) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и доступы для темы
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        if (!$this->cat_access->is_moder && !$this->cat_access->is_can_read) {
            return $this->halt(LANG_FORUM_NO_READ_THIS_FORUM);
        }

        return $this->halt($post['content_html']);
    }

}
