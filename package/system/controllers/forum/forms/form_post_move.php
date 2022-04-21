<?php

class formForumPostMove extends cmsForm {

    public function init() {

        return [

            'basic' => [
                'type' => 'fieldset',
                'childs' => [

                    new fieldList('cat_id', [
                        'title' => LANG_FORUM_CATS,
                        'generator' => function ($item, $request){

                            $list = ['' => ''];

                            $user = cmsUser::getInstance();
                            $model = cmsCore::getModel('forum');

                            if (!$user->is_admin){
                                $model->filterEqual('c.is_pub', 1);
                            }

                            $cats = $model->getCategoriesTree('forum', false);

                            if ($cats) {
                                foreach ($cats as $cat) {

                                    $cat['moderators'] = cmsModel::yamlToArray($cat['moderators']);

                                    if (!$user->isInGroups($cat['moderators']) && !$user->is_admin) {
                                        continue;
                                    }

                                    if ($cat['ns_level'] > 1) {
                                        $cat['title'] = str_repeat('-', $cat['ns_level']) . ' ' . $cat['title'];
                                    }
                                    $list[$cat['id']] = $cat['title'];
                                }
                            }
                            return $list;
                        },
                        'rules' => array(
                            array('required')
                        )
                    ]),

                    new fieldList('thread_id', array(
                        'title' => LANG_FORUM_MOVE_POST_IN_THREAD,
                        'parent' => array(
                            'list' => 'cat_id',
                            'url'  => href_to('forum', 'category_threads_list')
                        ),
                        'generator' => function($item, $request) {
                            $list     = ['' => ''];
                            $cat_id = !empty($item['cat_id']) ? $item['cat_id'] : 0;
                            if (!$cat_id && $request) {
                                $cat_id = $request->get('cat_id', 0);
                            }
                            if (!$cat_id) {
                                return $list;
                            }

                            $user = cmsUser::getInstance();
                            $model = cmsCore::getModel('forum');

                            if (!$user) {
                                $model->filterIsNull('is_deleted');
                            }

                            $threads = $model->selectOnly('title')->select('id')->limit(false)->
                                    filterEqual('category_id', $cat_id)->get('forum_threads');

                            if ($threads) {
                                foreach ($threads as $thread) {
                                    $list[$thread['id']] = $thread['title'];
                                }
                            }
                            return $list;
                        },
                        'rules' => array(
                            array('required')
                        )
                    ))

                ]

            ]

        ];
    }

}
