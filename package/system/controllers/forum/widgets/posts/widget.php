<?php

class widgetForumPosts extends cmsWidget {

    private $forum;
    private $user;

    // Категории, по которым фильтруем
    private $filtered_cats = [];
    // Запрещённые категории
    private $denied_cats = [];
    // Текущая тема
    private $current_thread = [];

    public function __construct($widget){

        parent::__construct($widget);

        $this->forum = cmsCore::getController('forum');
        $this->user = cmsUser::getInstance();
    }

    public function run() {

        $category_id = $this->getOption('category_id', 0);
        $only_thread = $this->getOption('only_thread', false);

        $category = [];

        if(!$only_thread){
            // Автодетект
            if (!$category_id) {
                $category = cmsModel::getCachedResult('current_forum_category');
                if(!$category){ return false; }
            } elseif($category_id > 1){
                $category = $this->forum->model->getCategoryByField($category_id);
            }
        } else {
            // Показываем только если виджет на странице темы
            $this->current_thread = cmsModel::getCachedResult('current_forum_thread');
            if(!$this->current_thread){
                return false;
            }
        }

        // Загружаем доступ к разделу
        if(!empty($category['path'])){
            $this->forum->loadCatAccess($category['path']);
            if (!$this->forum->cat_access->is_can_read) {
                return false;
            }
        }

        // Если категория задана, формируем список id
        if($category){

            // Включены подкатегории
            if ($this->getOption('is_nested_category', false)) {

                $subcats = $this->forum->model->getCategoryChilds($category, false, $this->forum->getChildsAccessCallback());
                if($subcats){
                    $this->filtered_cats = array_keys($subcats);
                }
            }

            // Выбранная категория
            $this->filtered_cats[] = $category['id'];
        } else {

            if (!$this->user->is_admin) {
                $this->forum->model->filterEqual('is_pub', 1);
            }

            // Иначе формируем список запрещённых категорий
            $subcats = $this->forum->model->getCategories($this->forum->getChildsAccessCallback(true));
            if($subcats){
                $this->denied_cats = array_keys($subcats);
            }
        }

        // Показывать по одному сообщению из темы
        if ($this->getOption('group_by_threads', false)) {

            if(!$this->filterByThread()){
                return false;
            }
        } else {
            $this->filterByPost();
        }

        $this->forum->model->join('forum_threads', 't', 't.id = i.thread_id');

        $this->forum->model->select('t.title', 'title');
        $this->forum->model->select('t.is_closed', 'is_closed');
        $this->forum->model->select('t.is_fixed', 'is_fixed');
        $this->forum->model->select('t.is_pinned', 'is_pinned');
        $this->forum->model->select('t.is_vip', 'is_vip');
        $this->forum->model->select('t.slug', 'thread_slug');

        $this->forum->model->joinUser();
        $this->forum->model->joinSessionsOnline();

        $items = $this->forum->model->get('forum_posts', function ($item, $model){

            $item['badges'] = modelForum::getThreadBadges($item);

            $item['user'] = [
                'id'        => $item['user_id'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => $item['user_avatar'],
                'slug'      => $item['user_slug'],
                'is_online' => $item['is_online']
            ];

            return $item;
        }, false);

        if (!$items) {
            return false;
        }

        return [
            'category'  => $category,
            'length'    => $this->getOption('length', false),
            'show_rating' => $this->getOption('show_rating', false),
            'show_text' => $this->getOption('show_text', false),
            'items'     => $items
        ];
    }

    private function filterByPost() {

        $rating_min = $this->getOption('rating_min', '');
        $rating_max = $this->getOption('rating_max', '');
        $sorting    = $this->getOption('sorting', 'date_pub:desc');

        if (empty($sorting)){
            $sorting = 'date_pub:desc';
        }

        // Есть категории для фильтрации
        if($this->filtered_cats){
            $this->forum->model->filterIn('i.category_id', $this->filtered_cats);
        }
        // Запрещённые категории
        if($this->denied_cats){
            $this->forum->model->filterNotIn('i.category_id', $this->denied_cats);
        }

        // Мы на странице темы
        if($this->current_thread){
            $this->forum->model->filterEqual('thread_id', $this->current_thread['id']);
        }

        if (!$this->user->is_admin) {
            $this->forum->model->filterIsNull('is_deleted');
        }

        $this->forum->model->limit($this->getOption('limit', 10));

        list($order_field, $order_direction) = explode(':', $sorting);

        if($order_field == 'rating'){

            if ($order_direction == 'desc' && $rating_min !== '') {
                $this->forum->model->filterGtEqual('rating', $rating_min);
            }
            if ($order_direction == 'asc' && $rating_max !== '') {
                $this->forum->model->filterLtEqual('rating', $rating_max);
            }
        }

        $this->forum->model->orderBy($order_field, $order_direction);

        return true;
    }

    private function filterByThread() {

        $sorting = $this->getOption('sorting_by_threads', 'date_pub:desc');
        list($order_field, $order_direction) = explode(':', $sorting);

        $this->forum->model->selectOnly('last_post');

        // Есть категории для фильтрации
        if($this->filtered_cats){
            $this->forum->model->filterIn('category_id', $this->filtered_cats);
        }
        // Запрещённые категории
        if($this->denied_cats){
            $this->forum->model->filterNotIn('category_id', $this->denied_cats);
        }

        if (!$this->user->is_admin) {
            $this->forum->model->filterIsNull('is_deleted');
        }

        $this->forum->model->limit($this->getOption('limit', 10));

        $this->forum->model->orderBy($order_field, $order_direction);

        $post_ids = $this->forum->model->get('forum_threads', function($item, $model){
            $item['last_post'] = cmsModel::yamlToArray($item['last_post']);
            return $item['last_post']['id'];
        }, false) ?: [];

        if(!$post_ids){
            return false;
        }

        $this->forum->model->filterIn('i.id', $post_ids);

        $this->forum->model->orderBy($order_field, $order_direction);

        return true;
    }

}
