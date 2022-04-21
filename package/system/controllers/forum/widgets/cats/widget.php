<?php

class widgetForumCats extends cmsWidget {

    public $is_cacheable = false;

    public function run() {

        $category_id    = $this->getOption('category_id');
        $show_full_tree = $this->getOption('show_full_tree');

        $active_cat = [];

        $current_category = cmsModel::getCachedResult('current_forum_category');

        // если автоматически определяем раздел форума и раздел просматривается
        if ($category_id == 0 && !empty($current_category['id'])) {
            $category_id = $current_category['id'];
        }
        if ($category_id <= 1) {
            $category_id = 1;
        }

        if (!empty($current_category['id'])) {
            $active_cat = $current_category;
        }

        $forum_controller = cmsCore::getController('forum');

        // Получаем данные раздела
        $category = $forum_controller->model->getCategoryByField($category_id);
        if (!$category) {
            return false;
        }

        // Загружаем доступ к разделу
        if($category['path']){
            $forum_controller->loadCatAccess($category['path']);
            if (!$forum_controller->cat_access->is_can_read) {
                return false;
            }
        }

        // Получаем список разделов отсортированных по уровням вложенности
        // и проверяем пользователя на доступ к просмотру их
        $cats = $forum_controller->model->getCategoryChilds($category, $forum_controller->options['cats_level_view'], $forum_controller->getChildsAccessCallback());

        if (!$cats) {
            return false;
        }

        if ($active_cat) {
            $path = array_filter($cats, function ($cat) use ($active_cat) {
                return ($cat['ns_left'] <= $active_cat['ns_left'] &&
                $cat['ns_level'] <= $active_cat['ns_level'] &&
                $cat['ns_right'] >= $active_cat['ns_right'] &&
                $cat['ns_level'] > 0);
            });
        }

        return [
            'show_full_tree'=> $show_full_tree,
            'cats'       => $cats,
            'active_cat' => $active_cat,
            'path'       => (!empty($path) ? $path : [])
        ];
    }

}
