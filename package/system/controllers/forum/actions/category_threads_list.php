<?php

class actionForumCategoryThreadsList extends cmsAction {

    public function run() {

        if (!$this->request->isAjax()) {
            return cmsCore::error404();
        }

        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        $list = [['title' => '', 'value' => '']];

        $cat_id = $this->request->get('value', 0);
        if (!$cat_id) {
            return $this->cms_template->renderJSON($list);
        }

        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        $threads = $this->model->selectOnly('title')->select('id')->limit(false)->
                filterEqual('category_id', $cat_id)->get('forum_threads');

        if ($threads) {
            foreach ($threads as $thread) {
                $list[] = ['title' => $thread['title'], 'value' => $thread['id']];
            }
        }

        return $this->cms_template->renderJSON($list);
    }

}
