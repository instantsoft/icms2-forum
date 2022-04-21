<?php

class actionForumPollVote extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_GUEST_NOT_VOTED]);
        }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!$csrf_token || !cmsForm::validateCSRFToken($csrf_token)) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_BAD_CSRF_TOKEN]);
        }

        $poll_id = $this->request->get('poll_id', 0);

        // Если нет голоса или не определен опрос - выходим
        if (!$poll_id) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_POLL_IS_DELETE]);
        }

        // Получаем данные по опросу
        $poll = $this->model->getThreadPoll($poll_id, $this->cms_user, 'id');
        if (!$poll) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_POLL_IS_DELETE]);
        }

        if ($poll['user_answer_ids']) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_YOU_IS_VOTE]);
        }

        if ($poll['is_closed']) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_POLL_FINISHED]);
        }

        // Получаем данные по теме форума
        $thread = $this->model->getThreadByField($poll['thread_id']);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        if ($thread['is_closed']) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_YOU_IS_NOT_VOTE_IN_CLOSED]);
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        $this->loadCatAccess($category['path']);

        if (!$this->cat_access->is_can_read) {
            return cmsCore::error404();
        }

        $answer_id = $this->request->get('answer', ($poll['options']['multi_answer'] ? [] : 0));

        // Если нет голоса - выходим
        if (!$answer_id) {
            return $this->cms_template->renderJSON(['error' => true, 'text' => LANG_FORUM_SELECT_THE_OPTION]);
        }

        // Если пользователь выбрал несколько вариантов ответа
        if (is_array($answer_id)) {

            foreach ($answer_id as $answ_id) {

                if(!$answ_id || !is_numeric($answ_id)){
                    continue;
                }

                $this->model->addPollVote([
                    'poll_id'   => $poll['id'],
                    'user_id'   => $this->cms_user->id,
                    'answer_id' => $answ_id
                ]);
            }
        } elseif(is_numeric($answer_id)) {

            $this->model->addPollVote([
                'poll_id'   => $poll['id'],
                'user_id'   => $this->cms_user->id,
                'answer_id' => $answer_id
            ]);
        }

        return $this->cms_template->renderJSON(['error' => false, 'text' => '']);
    }

}
