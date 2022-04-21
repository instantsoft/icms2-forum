<?php

class formWidgetForumThreadsOptions extends cmsForm {

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
                                0 => LANG_ALL
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
                        'title' => LANG_WD_FORUM_THREADS_SUBCATEGORY,
                        'default' => true,
                        'visible_depend' => ['options:category_id' => ['hide' => ['0']]]
                    )),

                    new fieldList('options:sorting', array(
                        'title' => LANG_SORTING,
                        'items' => [
                            'date_pub:desc'           => LANG_WD_FORUM_THREADS_DATASET_DATE_PUB,
                            'hits:desc'               => LANG_WD_FORUM_THREADS_DATASET_POPULAR,
                            'date_last_modified:desc' => LANG_WD_FORUM_THREADS_DATASET_LAST_MODIFY,
                            'posts_count:desc'        => LANG_WD_FORUM_THREADS_DATASET_POSTS_COUNT
                        ]
                    )),

                    new fieldNumber('options:limit', array(
                        'title' => LANG_LIST_LIMIT,
                        'default' => 10,
                        'rules' => array(
                            array('required')
                        )
                    ))

                )

            )

        );

    }

}
