<?php

class onForumSubscriptionMatchList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($subscription, $post_ids){

        // результирующий список
        $match_list = [];

        // фильтры по ячейкам таблицы
        if(!empty($subscription['params']['filters'])){

            foreach ($subscription['params']['filters'] as $filters) {
                // проверяем наличие ячеек и заполняем фильтрацию
                if($this->model->db->isFieldExists('forum_posts', $filters['field'])){
                    $params[] = $filters;
                }
            }
        }

        /**
         * Начинаем собирать запрос SQL
         */
        $this->model->limit(false);

        $this->model->selectOnly('i.id');
        $this->model->select('i.content_html');

        $this->model->filterIn('id', $post_ids);

        // фильтр по набору фильтров
        if($params){
            $this->model->applyDatasetFilters([
                'filters' => $params
            ], true);
        }

        $found_items = $this->model->get('forum_posts');

        if($found_items){
            foreach ($found_items as $item) {
                $match_list[] = [
                    'url'       => href_to_abs('forum', 'pfind', [$item['id']]),
                    'image_src' => '',
                    'title'     => string_short($item['content_html'], 150)
                ];
            }
        }

        return $match_list;
    }

}
