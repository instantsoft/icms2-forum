<?php

class onForumFavoritesSubject extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($target_subject_id, $page_url) {

        if ($target_subject_id != 1) {
            return '';
        }

        $this->model->join('favorites', 'fav', "fav.item_id = i.id AND fav.subject_id = '{$target_subject_id}' AND fav.controller = 'forum'")->
                filterEqual('fav.user_id', $this->cms_user->id);

        return $this->renderForumPostsList($page_url);
    }

}
