<?php

class widgetForumStats extends cmsWidget {

    private $forum;

    // Категории, по которым фильтруем
    private $filtered_cats = [];

    public function __construct($widget){

        parent::__construct($widget);

        $this->forum = cmsCore::getController('forum');
    }

    public function run() {

        $category_id     = $this->getOption('category_id');
        $show_month_stat = $this->getOption('show_month_stat');
        $show_all_stat   = $this->getOption('show_all_stat');
        $show_moderators = $this->getOption('show_moderators');

        $is_auto = !$category_id;

        $counter = [
            'all' => [
                'title' => LANG_WD_FORUM_STATS_ALL . ' '. ($is_auto ? LANG_WD_FORUM_STATS_THIS_CAT : LANG_WD_FORUM_STATS_FORUM),
                'show' => $show_all_stat,
                'counters' => [
                    'cats_count' => [
                        'title' => LANG_WD_FORUM_STATS_CATS_COUNT,
                        'count' => 0
                    ],
                    'threads_count' => [
                        'title' => LANG_WD_FORUM_STATS_THREADS_COUNT,
                        'count' => 0
                    ],
                    'posts_count' => [
                        'title' => LANG_WD_FORUM_STATS_POSTS_COUNT,
                        'count' => 0
                    ]
                ]
            ],
            'month' => [
                'title' => LANG_WD_FORUM_STATS_IN_MONTH,
                'show' => $show_month_stat,
                'counters' => [
                    'threads_count' => [
                        'title' => LANG_WD_FORUM_STATS_THREADS_COUNT,
                        'count' => 0
                    ],
                    'posts_count' => [
                        'title' => LANG_WD_FORUM_STATS_POSTS_COUNT,
                        'count' => 0
                    ]
                ]
            ],
        ];

        if ($is_auto) {
            $category = cmsModel::getCachedResult('current_forum_category');
        } else {
            $category = $this->forum->model->getCategoryByField($category_id);
        }

        if (!$category) {
            return false;
        }

        // Загружаем доступ к разделу
        if(!empty($category['path'])){
            $this->forum->loadCatAccess($category['path']);
            if (!$this->forum->cat_access->is_can_read) {
                return false;
            }
        }

        $subcats = $this->forum->model->getCategoryChilds($category, false, $this->forum->getChildsAccessCallback());

        $this->filtered_cats = array_keys($subcats);
        $this->filtered_cats[] = $category['id'];

        // Вся статистика
        if($show_all_stat){

            $counter['all']['counters']['cats_count']['count'] = count($subcats);
            $counter['all']['counters']['threads_count']['count'] = $this->getThreadsCount();
            $counter['all']['counters']['posts_count']['count'] = $this->getPostsCount();
        }

        // За последний месяц
        if($show_month_stat){

            $counter['month']['counters']['threads_count']['count'] = $this->getThreadsCount(true);
            $counter['month']['counters']['posts_count']['count'] = $this->getPostsCount(true);
        }

        // Показывать модераторов
        $moderators = [];
        if($show_moderators){
            $moderators = $this->getCategoryModerators($category['moderators']);
        }

        return [
            'show_moder_caption' => $show_all_stat || $show_month_stat,
            'category'   => $category,
            'counter'    => $counter,
            'moderators' => $moderators
        ];
    }

    private function getThreadsCount($is_last_month = false) {

        if($is_last_month){
            $this->forum->model->filterDateYounger('date_pub', 1, 'MONTH');
        }

        $this->forum->model->filterIsNull('is_deleted');

        if($this->filtered_cats){
            $this->forum->model->filterIn('category_id', $this->filtered_cats);
        }

        return $this->forum->model->getCount('forum_threads', 'id', true);
    }

    private function getPostsCount($is_last_month = false) {

        if($is_last_month){
            $this->forum->model->filterDateYounger('date_pub', 1, 'MONTH');
        }

        $this->forum->model->filterIsNull('is_deleted');

        if($this->filtered_cats){
            $this->forum->model->filterIn('category_id', $this->filtered_cats);
        }

        return $this->forum->model->getCount('forum_posts', 'id', true);
    }

    private function getCategoryModerators($user_ids) {

        $user_ids = array_filter((array)$user_ids);

        $all_moderators = $this->getOption('show_all_moderators') ? cmsPermissions::getRulesGroupMembers('forum', 'is_moderator', 1) : [];

        if (!$user_ids){ return $all_moderators; }

        $this->forum->model->selectOnly('i.id');
        $this->forum->model->select('i.nickname');
        $this->forum->model->select('i.slug');
        $this->forum->model->select('i.avatar');

        $this->forum->model->joinSessionsOnline('i');

        return array_merge($all_moderators, ($this->forum->model->filterIn('id', array_filter($user_ids))->get('{users}', function($item, $model){
            return [
                'id'        => $item['id'],
                'nickname'  => $item['nickname'],
                'slug'      => $item['slug'],
                'avatar'    => cmsModel::yamlToArray($item['avatar']),
                'is_online' => $item['is_online']
            ];
        }) ?: []));
    }

}
