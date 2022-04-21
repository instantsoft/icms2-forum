<?php
/**
 * Оставлено для совместимости с InatantCMS v. 1.10...
 */
class actionForumTfind extends cmsAction {

    public function run($thread_id) {

        // получаем данные по теме форума
        $thread = $this->model->getThreadByField($thread_id);

        if (!$thread || (!empty($thread['is_deleted']) && !$this->cms_user->is_admin)) {
            return cmsCore::error404();
        }

        $this->redirectTo('forum', $thread['slug'] . '.html', [], [], 301);
    }

}
