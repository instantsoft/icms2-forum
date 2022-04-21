<?php

class onForumAdminDashboardChart extends cmsAction {

    public function run() {

        return [
            'id' => 'forum',
            'title' => LANG_FORUM_FORUMS,
            'sections' => [
                'threads' => [
                    'title' => LANG_FORUM_THREADS,
                    'table' => 'forum_threads',
                    'key'   => 'date_pub'
                ],
                'posts'   => [
                    'title' => LANG_FORUM_POSTS,
                    'table' => 'forum_posts',
                    'key'   => 'date_pub'
                ]
            ],
            'footer' => [
                'posts' => [
                    [
                        'table' => 'forum_posts',
                        'title' => LANG_CP_ALL_PCOUNT,
                        'progress' => 'success',
                        'filters' => [
                            [
                                'condition' => 'ni',
                                'value'     => 1,
                                'field'     => 'is_deleted'
                            ]
                        ]
                    ],
                    [
                        'table' => 'forum_posts',
                        'title' => LANG_CP_DEL_PCOUNT,
                        'progress' => 'danger',
                        'filters' => [
                            [
                                'condition' => 'eq',
                                'value'     => 1,
                                'field'     => 'is_deleted'
                            ]
                        ]
                    ]
                ],
                'threads' => [
                    [
                        'table' => 'forum_threads',
                        'title' => LANG_CP_ALL_PTHR,
                        'progress' => 'success',
                        'filters' => [
                            [
                                'condition' => 'ni',
                                'value'     => 1,
                                'field'     => 'is_deleted'
                            ]
                        ]
                    ],
                    [
                        'table' => 'forum_threads',
                        'title' => LANG_CP_DEL_PTHR,
                        'progress' => 'danger',
                        'filters' => [
                            [
                                'condition' => 'eq',
                                'value'     => 1,
                                'field'     => 'is_deleted'
                            ]
                        ]
                    ]
                ],
            ]
        ];
    }

}
