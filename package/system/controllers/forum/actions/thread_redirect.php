<?php
/**
 * Переадресация с URL страниц форума InstantCMS v. 1.10
 */
class actionForumThreadRedirect extends cmsAction {

    public function run() {

        $thread_id = $this->request->get('thread_id', 0);
        $page = $this->request->get('page', 1);

        if (!$thread_id) { return cmsCore::error404(); }

        // Получаем данные по теме форума
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            cmsCore::error404();
        }

        // Перенаправляем на новый адрес
        $this->redirect(href_to('forum', $thread['slug'] . '.html' . ($page > 1 ? '?page=' . $page : '')), 301);
    }

}
