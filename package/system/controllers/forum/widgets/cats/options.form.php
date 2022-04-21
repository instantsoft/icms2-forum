<?php

class formWidgetForumCatsOptions extends cmsForm {

    public function init() {
        return [
            [
                'type'   => 'fieldset',
                'title'  => LANG_OPTIONS,
                'childs' => [
                    new fieldList('options:category_id', [
                        'title' => LANG_CATEGORY,
                        'default' => 1,
                        'generator' => function ($item) {

                            $tree = cmsCore::getModel('forum')->filterEqual('i.is_pub', 1)->getCategoriesTree('forum', false);

                            $items = [
                                0 => LANG_WD_FORUM_CATS_AUTODETECT,
                                1 => LANG_WD_FORUM_CATS_ROOT_CATEGORY
                            ];

                            if ($tree) {
                                foreach ($tree as $item) {
                                    $items[$item['id']] = str_repeat('-- ', ($item['ns_level']-1)) . $item['title'];
                                }
                            }

                            return $items;
                        }
                    ]),
                    new fieldCheckbox('options:show_full_tree', [
                        'title'   => LANG_WD_FORUM_CATS_SHOW_FULL_TREE,
                        'default' => false
                    ])
                ]
            ]
        ];
    }

}
