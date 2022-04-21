<?php

class actionForumLatestThreads extends cmsAction {

    public function run() {

        // Включена вкладка
        if(empty($this->options['show_ds_menu_lthr'])){
            return cmsCore::error404();
        }

        // Получаем номер просматриваемой страницы
        $page = $this->request->get('page', 1);

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

        $this->model->orderBy('i.date_pub', 'desc');

        // Если не администратор, скрываем удаленные темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        // Считаем общее количество найденных тем
        $total = $this->model->getCount('forum_threads');

        // Ограничиваем постраничный вывод
        $this->model->limitPage($page, $this->options['perpage_threads']);

        // Получаем последние темы
        $threads = $this->model->getThreads($this->options['fix_threads_reads']);

        // Бесконечное кол-во страниц нам не нужно
        if(!$threads && $page > 1){ cmsCore::error404(); }

        $datasets = $this->getDatasets();

        $dataset_name = 'latest_threads';

        $dataset = $datasets[$dataset_name];

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'latest_threads').'"/>');

        return $this->cms_template->render('latest_threads', [
            'base_ds_url'  => href_to($this->name) . '%s',
            'datasets'     => $datasets,
            'dataset_name' => $dataset_name,
            'dataset'      => $dataset,
            'page'         => $page,
            'perpage'      => $this->options['perpage_threads'],
            'total'        => $total,
            'threads'      => $threads,
            'user'         => $this->cms_user,
            'options'      => $this->options
        ]);
    }

}
