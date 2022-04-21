<?php

class actionForumMyPosts extends cmsAction {

    public function run() {

        // Включена вкладка
        if(empty($this->options['show_ds_menu_myp'])){
            return cmsCore::error404();
        }

        // Доступно авторизованнм пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Берем только сообщения пользователя
        $this->model->filterEqual('user_id', $this->cms_user->id);

        $list_html = $this->renderForumPostsList();

        $datasets = $this->getDatasets();

        $dataset_name = 'my_posts';

        $dataset = $datasets[$dataset_name];

        return $this->cms_template->render('my_posts', [
            'list_html'    => $list_html,
            'base_ds_url'  => href_to($this->name) . '%s',
            'datasets'     => $datasets,
            'dataset_name' => $dataset_name,
            'dataset'      => $dataset,
            'user'         => $this->cms_user
        ]);
    }

}
