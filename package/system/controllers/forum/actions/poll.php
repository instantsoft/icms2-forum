<?php

class actionForumPoll extends cmsAction {

    public function run($thread_id, $action = 'form') {

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
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);;

        if (!$this->cat_access->is_can_read) {
            return cmsCore::error404();
        }

        // Получаем данные по опросу
        $thread_poll = $this->model->getThreadPoll($thread['id'], $this->cms_user);
        if (!$thread_poll) {
            return cmsCore::error404();
        }

        // Проверяем настройки голосования, если гость и нет опции показа гостям, выходим
        if (!$this->cms_user->is_logged && $thread_poll['options']['result']) {
            return cmsCore::error404();
        }

        // Если пользователь сменил свой голос и это доступно в настройках форума, то удаляем его старый голос
        if ($this->cms_user->is_logged && !$thread_poll['is_closed'] && $thread_poll['user_answer_ids'] && $thread_poll['options']['change'] && $action == 'revote') {

            $this->model->deleteVote($thread_poll['id'], $this->cms_user->id);

            // Перегружаем данные по опросу
            $thread_poll = $this->model->getThreadPoll($thread['id'], $this->cms_user);
        }

        // Передаем данные в шаблон
        return $this->cms_template->render('thread_poll', [
            'thread'      => $thread,
            'thread_poll' => $thread_poll,
            'thread_access' => $this->thread_access,
            'show_result' => $thread_poll['allow_show_result'] && $action == 'result'
        ]);
    }

}
