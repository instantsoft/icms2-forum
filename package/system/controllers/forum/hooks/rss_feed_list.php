<?php

class onForumRssFeedList extends cmsAction {

    public $disallow_event_db_register = true;

    public function run($feed) {

        $category_id = $this->request->get('category', 1);

        $view_type = $this->request->get('view', 'threads');

        // Получаем данные по корневому разделу
        $category = $this->model->getCategoryByField($category_id);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу
        if(!empty($category['path'])){
            $this->loadCatAccess($category['path']);
            if (!$this->cat_access->is_can_read) {
                return cmsCore::error404();
            }
        }

        // Получаем данные о всех подразделах
        $subcats = $this->model->getCategoryChilds($category, false, $this->getChildsAccessCallback());
        if($subcats){
            $this->model->filterIn('i.category_id', array_keys($subcats));
        }

        $this->model->filterIsNull('is_deleted');

        $this->model->orderBy('date_pub', 'desc');

        $this->model->limit($feed['limit']);

        if ($view_type == 'threads') {

            $feed['items'] = $this->model->getThreads();

        } else {

            $this->model->joinLeft('forum_threads', 't', 't.id = i.thread_id');

            $this->model->select('t.title', 'title');

            $this->model->joinUser();

            $feed['items'] = $this->model->get('forum_posts', function ($item, $model){

                $item['user'] = [
                    'id'        => $item['user_id'],
                    'nickname'  => $item['user_nickname'],
                    'avatar'    => $item['user_avatar'],
                    'slug'      => $item['user_slug']
                ];

                return $item;
            }, false);
        }

        $feed = cmsEventsManager::hook('before_render_forum_feed_list', $feed);

        $feed['view'] = $view_type;

        return [$feed, $category, []];
    }

}
