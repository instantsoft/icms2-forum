<?php

class widgetForumThreads extends cmsWidget {

    // Категории, по которым фильтруем
    private $filtered_cats = [];
    // Запрещённые категории
    private $denied_cats = [];

    public function run() {

        $category_id = $this->getOption('category_id');

        list($order_field, $order_direction) = explode(':', $this->getOption('sorting', 'date_pub:desc'));

        $forum = cmsCore::getController('forum');

        $category = [];

        if ($category_id) {
            $category = $forum->model->getCategoryByField($category_id);
            if (!$category) {
                return false;
            }
        }

        // Загружаем доступ к разделу
        if(!empty($category['path'])){
            $forum->loadCatAccess($category['path']);
            if (!$forum->cat_access->is_can_read) {
                return false;
            }
        }

        // Если категория задана, формируем список id
        if($category){

            // Включены подкатегории
            if ($this->getOption('is_nested_category', false)) {

                $subcats = $forum->model->getCategoryChilds($category, false, $forum->getChildsAccessCallback());
                if($subcats){
                    $this->filtered_cats = array_keys($subcats);
                }
            }

            // Выбранная категория
            $this->filtered_cats[] = $category['id'];
        } else {

            if (!cmsUser::isAdmin()) {
                $forum->model->filterEqual('is_pub', 1);
            }

            // Иначе формируем список запрещённых категорий
            $subcats = $forum->model->getCategories($forum->getChildsAccessCallback(true));
            if($subcats){
                $this->denied_cats = array_keys($subcats);
            }
        }

        // Есть категории для фильтрации
        if($this->filtered_cats){
            $forum->model->filterIn('i.category_id', $this->filtered_cats);
        }
        // Запрещённые категории
        if($this->denied_cats){
            $forum->model->filterNotIn('i.category_id', $this->denied_cats);
        }

        $forum->model->select('c.title', 'category_title');
        $forum->model->select('c.slug', 'category_slug');

        $forum->model->joinLeft('forum_cats', 'c', 'c.id = i.category_id');

        $forum->model->limit($this->getOption('limit', 10));

        $forum->model->orderBy($order_field, $order_direction);

        $forum->model->joinUser()->joinSessionsOnline();

        $forum->model->filterIsNull('is_deleted');

        $items = $forum->model->get('forum_threads', function ($item) {

            $item['title'] = modelForum::getThreadTitleWithPrefix($item);

            $item['user'] = [
                'id'        => $item['user_id'],
                'nickname'  => $item['user_nickname'],
                'avatar'    => $item['user_avatar'],
                'slug'      => $item['user_slug'],
                'is_online' => $item['is_online']
            ];

            $item['last_post'] = cmsModel::yamlToArray($item['last_post']);
            return $item;
        });

        if (!$items) {
            return false;
        }

        return [
            'category' => $category,
            'items'    => $items
        ];
    }

}
