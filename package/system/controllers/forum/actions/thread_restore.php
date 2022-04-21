<?php
/**
 * Восстановление темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThreadRestore extends cmsAction {

    public function run($thread_id) {

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken($csrf_token)) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread || !$thread['is_deleted']) {
            return cmsCore::error404();
        }

        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        $this->model->filterEqual('thread_id', $thread['id'])->
                updateFiltered('forum_posts', ['is_deleted' => null]);

        $this->model->updateThread($thread['id'], ['is_deleted' => null], $category);

        cmsEventsManager::hook('forum_thread_after_restore', [$thread, $category]);

        $this->redirectTo('forum', $thread['slug'] . '.html');
    }

}
