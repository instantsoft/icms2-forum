<?php

class onForumFulltextSearch extends cmsAction {

    public function run() {

        $sources = [
            'forum_threads' => LANG_FORUM_THREADS_ON_FORUM,
            'forum_posts'   => LANG_FORUM_POSTS_ON_FORUM
        ];

        $table_names = [
            'forum_threads' => 'forum_threads',
            'forum_posts'   => 'forum_posts'
        ];

        $match_fields = [
            'forum_threads' => ['title', 'description'],
            'forum_posts'   => ['content_html']
        ];

        $select_fields = [
            'forum_threads' => ['id', 'slug', 'title', 'description', 'date_pub', 'is_fixed', 'is_closed'],
            'forum_posts'   => ['id', 'thread_id', 'content_html', 'date_pub', 't.title']
        ];

        $joins = [
            'forum_posts' => [
                'joinInner' => [
                    'forum_threads', 't', 'i.thread_id = t.id'
                ]
            ]
        ];

        $filters = [];

        // Формируем список запрещённых категорий
        if (!$this->cms_user->is_admin) {
            $this->model->filterEqual('is_pub', 1);
        }
        $subcats = $this->model->getCategories($this->getChildsAccessCallback(true));
        if($subcats){

            $denied_cats = array_keys($subcats);

            foreach ($denied_cats as $denied_cat_id) {
                $filters[] = [
                    'field'     => 'category_id',
                    'condition' => '<>',
                    'value'     => $denied_cat_id
                ];
            }
        }

        $filters[] = [
            'field'     => 'is_deleted',
            'condition' => 'IS',
            'value'     => NULL
        ];

        return [
            'name'          => $this->name,
            'sources'       => $sources,
            'table_names'   => $table_names,
            'match_fields'  => $match_fields,
            'select_fields' => $select_fields,
            'joins'         => $joins,
            'highlight_fields' => [
                'forum_posts' => ['content_html', 'title']
            ],
            'filters'       => [
                'forum_threads' => $filters,
                'forum_posts'   => $filters
            ],
            'item_callback' => function ($item, $model, $sources_name, $match_fields, $select_fields) {

                if ($sources_name == 'forum_threads') {

                    return array_merge($item, [
                        'url'      => href_to('forum', $item['slug'] . '.html'),
                        'title'    => $item['title'],
                        'fields'   => [$item['description']],
                        'date_pub' => $item['date_pub'],
                        'image'    => ''
                    ]);

                } else {

                    return array_merge($item, [
                        'url'      => href_to('forum', 'pfind', [$item['id']]),
                        'title'    => $item['title'],
                        'fields'   => [$item['content_html']],
                        'date_pub' => $item['date_pub'],
                        'image'    => ''
                    ]);
                }
            }
        ];
    }

}
