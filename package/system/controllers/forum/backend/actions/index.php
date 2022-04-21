<?php
/**
 * Страница списка разделов в админке
 */
class actionForumIndex extends cmsAction {

    public function run(){

        $tree_path = cmsUser::getCookie('forum_tree_path');

        $category = [];

        if($tree_path && ($tree_path = explode('/', $tree_path)) && !empty($tree_path[2]) && ($cat_id = (int)$tree_path[2])){
            $category = $this->model->getCategory('forum', $cat_id);
        }

        if(!empty($category)){
            $grid = $this->loadDataGrid('cats', [$category], 'admin.grid_filter.forum');
        } else {
            $grid = $this->loadDataGrid('cats', [$category]);
        }

        return $this->cms_template->render('backend/index', [
            'grid' => $grid
        ]);
    }

}
