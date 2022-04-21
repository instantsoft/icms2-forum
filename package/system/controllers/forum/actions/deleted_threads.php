<?php

class actionForumDeletedThreads extends cmsAction {

    public function run() {

        if (!$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Получаем номер просматриваемой страницы
        $page = $this->request->get('page', 1);

        $this->model->orderBy('i.date_pub', 'desc');

        $this->model->filterNotNull('is_deleted');

        // Считаем общее количество найденных тем
        $total = $this->model->getCount('forum_threads');

        // Ограничиваем постраничный вывод
        $this->model->limitPage($page, $this->options['perpage_threads']);

        // Получаем последние темы
        $threads = $this->model->getThreads($this->options['fix_threads_reads']);

        // Бесконечное кол-во страниц нам не нужно
        if(!$threads && $page > 1){ cmsCore::error404(); }

        $datasets = $this->getDatasets();

        $dataset_name = 'deleted_threads';

        $dataset = $datasets[$dataset_name];

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'deleted_threads').'"/>');

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
