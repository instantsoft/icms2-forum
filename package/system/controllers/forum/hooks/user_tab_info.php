<?php

class onForumUserTabInfo extends cmsAction {

    public function run($profile, $tab_name) {

        if (!$this->cms_user->isPrivacyAllowed($profile, 'view_user_forum_posts')) {
            return false;
        }

        if (!$this->cms_user->is_admin) {
            $this->model->filterEqual('is_pub', 1);
        }

        // Получаем данные по недоступным разделам
        $categories = $this->model->getCategories($this->getChildsAccessCallback(true));

        if($categories){
            $this->model->filterNotIn('i.category_id', array_keys($categories));
        }

        $this->model->filterIsNull('is_deleted');

        $this->count = $this->model->
                filterEqual('user_id', $profile['id'])->
                getPostsCount();

        if (!$this->count) {
            return false;
        }

        return ['counter' => $this->count];
    }

}
