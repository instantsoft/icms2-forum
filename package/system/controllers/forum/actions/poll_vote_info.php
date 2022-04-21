<?php

class actionForumPollVoteInfo extends cmsAction{

    public function run($poll_id, $answer_id){

        if (!$this->request->isAjax() || !is_numeric($answer_id)) {
            return cmsCore::error404();
        }

        // Получаем данные по опросу
        $poll = $this->model->getThreadPoll($poll_id, $this->cms_user, 'id');
        if (!$poll) {
            return cmsCore::error404();
        }

        if (!$poll['options']['answers_is_pub'] || !isset($poll['results']['answers'][$answer_id]) || !$poll['allow_show_result']) {
            return cmsCore::error404();
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

        // Флаг что нужно вывести только голый список
        $is_list_only = $this->request->get('is_list_only');

        $page = $this->request->get('page', 1);
        $perpage = 10;

        $this->model->filterEqual('poll_id', $poll_id)->
                filterEqual('answer_id', $answer_id)->
                limitPage($page, $perpage);

        $total = $this->model->getPollVotesUserCount();
        $votes = $this->model->getPollVotesUsers();

        $pages = ceil($total / $perpage);

        if ($is_list_only){
            return $this->cms_template->render('poll_vote_info_list', [
                'votes' => $votes
            ]);
        }

        return $this->cms_template->render([
            'votes'     => $votes,
            'answer_id' => $answer_id,
            'poll'      => $poll,
            'page'      => $page,
            'pages'     => $pages,
            'perpage'   => $perpage
        ]);
    }

}
