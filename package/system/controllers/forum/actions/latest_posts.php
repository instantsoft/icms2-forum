<?php

class actionForumLatestPosts extends cmsAction {

    public function run() {

        // Включена вкладка
        if(empty($this->options['show_ds_menu_lp'])){
            return cmsCore::error404();
        }

        // Получаем данные по корневому разделу
        $category = $this->model->getCategoryByField(1);
        if (!$category) {
            return cmsCore::error404();
        }

        // Получаем данные о всех подразделах
        $subcats = $this->model->getCategoryChilds($category, false, $this->getChildsAccessCallback());
        if($subcats){
            // Фильтруем только по разрешённым категориям
            $this->model->filterIn('i.category_id', array_keys($subcats));
        }

        $list_html = $this->renderForumPostsList();

        $datasets = $this->getDatasets();

        $dataset_name = 'latest_posts';

        $dataset = $datasets[$dataset_name];

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'latest_posts').'"/>');

        return $this->cms_template->render('latest_posts', [
            'base_ds_url'   => href_to($this->name) . '%s',
            'datasets'      => $datasets,
            'dataset_name'  => $dataset_name,
            'dataset'       => $dataset,
            'list_html'     => $list_html,
            'user'          => $this->cms_user
        ]);
    }

}
