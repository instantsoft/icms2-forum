<?php
/**
 * Пометка всех тем как просмотренных
 */
class actionForumAllThreadsView extends cmsAction {

    public function run($category_id) {

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем данные раздела
        $category = $this->model->getCategoryByField($category_id);
        if (!$category) {
            return $this->redirectBack();
        }

        // Если раздел как категория, возвращаемся
        if (!empty($category['as_folder'])) {
            return $this->redirectBack();
        }

        // Получаем список непросмотренных тем из данного раздела, включая подразделы
        $this->model->filterGtEqual('c.ns_left', $category['ns_left']);
        $this->model->filterLtEqual('c.ns_right', $category['ns_right']);

        $this->model->joinLeft('forum_cats', 'c', 'c.id = i.category_id');
        $this->model->joinExcludingLeft('forum_threads_hits', 'th', 'th.thread_id', 'i.id', "th.user_id = '{$this->cms_user->id}'");

        // Скрываем удаленные темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        $items = $this->model->selectOnly('i.id')->limit(false)->get('forum_threads', false, false);

        if (!$items) {
            return $this->redirectBack();
        }

        // Чтобы была запись на диск один раз, используем транзакцию
        $this->model->startTransaction();

        foreach ($items as $item) {
            $this->model->addThreadHit($item['id'], $this->cms_user->id);
        }

        $this->model->endTransaction(true);

        return $this->redirectBack();
    }

}
