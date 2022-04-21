<?php

class actionForumPfind extends cmsAction {

    public function run($post_id) {

        header('X-Frame-Options: DENY');

        // берём только нужное
        $this->model->selectOnly('i.id');
        $this->model->select('i.is_deleted');
        $this->model->select('i.is_pinned');
        $this->model->select('i.date_pub');
        $this->model->select('i.thread_id');
        $this->model->select('t.slug', 'thread_slug');

        // Получаем данные по сообщению
        $post = $this->model->join('forum_threads', 't', 't.id = i.thread_id')->
                getItemById('forum_posts', $post_id);

        // Если нет такого сообщения, возвращаемся обратно
        if (!$post || (!$this->cms_user->is_admin && !empty($post['is_deleted']))) {
            return cmsCore::error404();
        }

        // Если сообщение закреплено
        if ($post['is_pinned']) {
            $this->model->filterEqual('is_pinned', 1);
        }

        // Если не администратор, скрываем удаленные сообщения и темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        // Получаем количество сообщений от начала темы до искомого
        $posts_count = $this->model->
                filterBetween('date_pub', 0, $post['date_pub'])->
                getPostsCount($post['thread_id']);

        // Определяем номер страницы темы
        $page = ceil($posts_count / $this->options['perpage_posts']);

        // Переадресовываем пользователя на страницу сообщения
        $this->redirectTo('forum', $post['thread_slug'] . '.html' . ($page > 1 ? '?page=' . $page : '') . '#post-' . $post['id'], [], [], 301);
    }

}
