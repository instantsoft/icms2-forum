<?php

class actionForumMyThreads extends cmsAction {

    public function run() {

        // Включена вкладка
        if(empty($this->options['show_ds_menu_mythr'])){
            return cmsCore::error404();
        }

        // Доступно авторизованнм пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем номер просматриваемой страницы
        $page = $this->request->get('page', 1);

        // Получаем настройки сортировки тем
        $order_by = $this->request->get('order_by', 'date_pub');
        $order_to = $this->request->get('order_to', 'desc');
        if (!in_array($order_by, array('date_pub', 'title', 'posts_count', 'hits'))) {
            $order_by = 'date_pub';
        }
        if (!in_array($order_to, array('asc', 'desc'))) {
            $order_to = 'desc';
        }
        $daysprune = $this->request->get('daysprune', 0);

        if($daysprune){
            $this->model->filterDateYounger('date_pub', $daysprune);
        }

        // Берем только темы пользователя
        $this->model->filterEqual('user_id', $this->cms_user->id);

        // Скрываем удаленные темы
        if (!$this->cms_user->is_admin) {
            $this->model->filterIsNull('is_deleted');
        }

        // Считаем общее количество найденных тем
        $total = $this->model->getCount('forum_threads');

        // Ограничиваем постраничный вывод
        $this->model->limitPage($page, $this->options['perpage_threads']);

        $this->model->orderBy($order_by, $order_to);

        // Получаем темы пользователя
        $threads = $this->model->getThreads($this->options['fix_threads_reads']);

        $datasets = $this->getDatasets();

        $dataset_name = 'my_threads';

        $dataset = $datasets[$dataset_name];

        $filter = [
            'order_by' => [
                'title'       => LANG_TITLE,
                'date_pub'    => LANG_FORUM_MODIFY_DATE,
                'posts_count' => LANG_FORUM_ANSWER_COUNT,
                'hits'        => LANG_FORUM_HITS_COUNT
            ],
            'order_to' => [
                'asc'  => LANG_FORUM_ORDER_ASC,
                'desc' => LANG_FORUM_ORDER_DESC
            ],
            'daysprune' => [
                0   => LANG_FORUM_SHOW_ALL,
                1   => LANG_FORUM_SHOW_DAY,
                7   => LANG_FORUM_SHOW_W,
                30  => LANG_FORUM_SHOW_MONTH,
                365 => LANG_FORUM_SHOW_YEAR
            ]
        ];

        return $this->cms_template->render('my_threads', [
            'user'              => $this->cms_user,
            'base_ds_url'       => href_to($this->name) . '%s',
            'filter'            => $filter,
            'datasets'          => $datasets,
            'dataset_name'      => $dataset_name,
            'dataset'           => $dataset,
            'page'              => $page,
            'perpage'           => $this->options['perpage_threads'],
            'total'             => $total,
            'order_by'          => $order_by,
            'order_to'          => $order_to,
            'daysprune'         => $daysprune,
            'threads'           => $threads,
            'fix_threads_reads' => $this->options['fix_threads_reads'],
            'options'           => $this->options
        ]);
    }

}
