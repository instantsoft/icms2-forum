<?php
/**
 * Переход к первому непрочтенному сообщению в теме
 */
class actionForumNewpost extends cmsAction {

    public function run($thread_id, $post_id = false) {

        // Дата посещения сайта пользователем
        $user_date_log = strtotime(cmsUser::sessionGet('user:date_log'));

        if ($post_id) {

            // Если не администратор, скрываем удаленные сообщения и темы
            if (!$this->cms_user->is_admin) {
                $this->model->filterIsNull('is_deleted');
            }

            $newpost = $this->model->getItemById('forum_posts', $post_id);

            if ($newpost && $newpost['thread_id'] == $thread_id) {
                return $this->runExternalAction('pfind', [$newpost['id']]);
            } else {
                return cmsCore::error404();
            }
        }

        // Получаем данные по теме форума
        $thread = $this->model->getThreadByField($thread_id);

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!$thread || (!empty($thread['is_deleted']) && !$this->cms_user->is_admin)) {
            return cmsCore::error404();
        }

        // Если не администратор, скрываем удаленные сообщения и темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        // Ищем первое непрочитанное сообщение в теме
        $this->model->selectOnly('id');
        $this->model->filterEqual('thread_id', $thread['id']);
        $this->model->filterTimestampGt('date_pub', $user_date_log);

        $newpost = $this->model->getItem('forum_posts');

        // Первое сообщение
        if(!$newpost){

            $this->model->selectOnly('id');
            $this->model->filterEqual('thread_id', $thread['id']);
            $this->model->orderBy('date_pub', 'asc');

            $newpost = $this->model->getItem('forum_posts');
        }

        return $this->runExternalAction('pfind', [$newpost['id']]);
    }

}
