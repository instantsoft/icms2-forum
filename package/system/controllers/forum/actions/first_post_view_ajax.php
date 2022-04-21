<?php

class actionForumFirstPostViewAjax extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // Доступно при включенной опции компонента на предпросмотр
        if (empty($this->options['preview_thread'])) {
            return cmsCore::error404();
        }

        $thread_id = $this->request->get('thread_id', 0);
        if (!$thread_id) {
            return cmsCore::error404();
        }

        // Получаем данные сообщения
        $post = $this->model->
                filterEqual('thread_id', $thread_id)->
                filterEqual('is_first', 1)->
                getItem('forum_posts');

        if (!$post) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы и сообщения, доступны только администраторам сайта
        if (!$this->cms_user->is_admin && !empty($post['is_deleted'])) {
            return cmsCore::error404();
        }

        return $this->cms_template->renderJSON(['content' => $post['content_html']]);
    }

}
