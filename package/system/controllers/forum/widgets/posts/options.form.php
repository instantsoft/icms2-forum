<?php

class formWidgetForumPostsOptions extends cmsForm {

    public function init() {

        return array(

            array(
                'type' => 'fieldset',
                'title' => LANG_OPTIONS,
                'childs' => array(

                    new fieldList('options:category_id', array(
                        'title' => LANG_CATEGORY,
                        'default' => 1,
                        'generator' => function($item) {

                            $tree = cmsCore::getModel('forum')->filterEqual('i.is_pub', 1)->getCategoriesTree('forum', false);

                            $items = [
                                0 => LANG_WD_FORUM_POSTS_CATEGORY_DETECT,
                                1 => LANG_ALL
                            ];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = str_repeat('-- ', ($item['ns_level']-1)) . $item['title'];
                                }
                            }

                            return $items;
                        }
                    )),

                    new fieldCheckbox('options:is_nested_category', array(
                        'title' => LANG_WD_FORUM_POSTS_SUBCATEGORY,
                        'default' => true,
                        'visible_depend' => ['options:category_id' => ['hide' => ['0', '1']]]
                    )),

                    new fieldCheckbox('options:group_by_threads', array(
                        'title' => LANG_WD_FORUM_POSTS_GROUP_BY_THREADS,
                    )),

                    new fieldCheckbox('options:only_thread', array(
                        'title' => LANG_WD_FORUM_POSTS_ONLY_THREAD,
                        'hint' => LANG_WD_FORUM_POSTS_ONLY_THREAD_HINT,
                        'default' => false,
                        'visible_depend' => ['options:group_by_threads' => ['hide' => ['1']]]
                    )),

                    new fieldList('options:sorting', array(
                        'title' => LANG_WD_FORUM_POSTS_DATASET,
                        'items' => array(
                            'date_pub:desc' => LANG_WD_FORUM_POSTS_DATASET_DATE_PUB,
                            'rating:desc' => LANG_WD_FORUM_POSTS_RATING_DESC,
                            'rating:asc' => LANG_WD_FORUM_POSTS_RATING_ASC
                        ),
                        'visible_depend' => ['options:group_by_threads' => ['hide' => ['1']]]
                    )),

                    new fieldList('options:sorting_by_threads', array(
                        'title' => LANG_WD_FORUM_POSTS_DATASET,
                        'items' => array(
                            'date_last_modified:desc' => LANG_WD_FORUM_POSTS_DATASET_DATE_PUB,
                            'hits:desc' => LANG_WD_FORUM_POSTS_DATASET_HITS,
                            'posts_count:desc' => LANG_WD_FORUM_POSTS_DATASET_POSTS_COUNT
                        ),
                        'visible_depend' => ['options:group_by_threads' => ['show' => ['1']]]
                    )),

                    new fieldNumber('options:rating_min', array(
                        'title' => LANG_WD_FORUM_MIN_RATING,
                        'visible_depend' => ['options:group_by_threads' => ['hide' => ['1']], 'options:sorting' => ['show' => ['rating:desc']]]
                    )),

                    new fieldNumber('options:rating_max', array(
                        'title' => LANG_WD_FORUM_MAX_RATING,
                        'visible_depend' => ['options:group_by_threads' => ['hide' => ['1']], 'options:sorting' => ['show' => ['rating:asc']]]
                    )),

                    new fieldCheckbox('options:show_rating', array(
                        'title' => LANG_WD_FORUM_SHOW_RATING,
                        'default' => true,
                    )),

                    new fieldCheckbox('options:show_text', array(
                        'title' => LANG_WD_FORUM_SHOW_TEXT,
                        'default' => true,
                    )),

                    new fieldNumber('options:length', array(
                        'title' => LANG_WD_FORUM_POSTS_LENGTH,
                        'default' => 300,
                        'visible_depend' => ['options:show_text' => ['show' => ['1']]],
                        'rules' => [
                            ['required']
                        ]
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => [
                            ['required']
                        ]
                    ))

                )

            )

        );

    }

}
