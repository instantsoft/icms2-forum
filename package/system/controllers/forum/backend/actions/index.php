<?php

/**
 * Страница списка разделов в админке
 */
class actionForumIndex extends cmsAction {

    use icms\traits\controllers\actions\listgrid {
        getListItemsGridHtml as private traitGetListItemsGridHtml;
    }

    private $category      = [];
    private $tree_path_key = '';
    private $category_id   = 1;

    public function __construct($controller, $params = []) {

        parent::__construct($controller, $params);

        $this->table_name = 'forum_cats';
        $this->grid_name  = 'cats';
        $this->title      = LANG_FORUM_FORUMS;

        $this->toolbar_hook = 'admin_content_toolbar';

    }

    public function prepareRun() {

        $this->tree_path_key = cmsUser::getCookie('forum_tree_path');

        if ($this->tree_path_key) {
            $this->tree_path_key = explode('/', $this->tree_path_key);
            if (!empty($this->tree_path_key[2])) {
                $this->category_id = end($this->tree_path_key);
            }
        }

        $this->category  = $this->model->getCategory('forum', $this->category_id);
        $this->grid_args = [$this->category];

        $this->list_callback = function ($model) {

            $model->filterGt('ns_left', $this->category['ns_left']);
            $model->filterLt('ns_right', $this->category['ns_right']);
            $model->filterGt('parent_id', 0);

            return $model;
        };

        $this->tool_buttons = [
            [
                'class'        => 'folder',
                'childs_count' => 4,
                'title'        => LANG_FORUM_CATS
            ],
            [
                'class' => 'add_folder',
                'level' => 2,
                'title' => LANG_CP_FORUM_CAT_CREATE,
            ],
            [
                'class' => 'edit_folder',
                'level' => 2,
                'title' => LANG_FORUM_CAT_EDIT,
            ],
            [
                'class'   => 'delete_folder',
                'level'   => 2,
                'title'   => LANG_CP_FORUM_CAT_DELETE,
                'confirm' => LANG_CP_FORUM_CAT_DELETE_CONFIRM,
            ],
            [
                'class'   => 'tree_folder',
                'level'   => 2,
                'title'   => LANG_CP_FORUM_CAT_ORDER,
                'href'    => $this->cms_template->href_to('category_order'),
                'onclick' => 'return categoryReorder($(this));',
            ],
        ];

    }

    public function getListItemsGridHtml() {

        $grid_html = $this->traitGetListItemsGridHtml();

        return $this->cms_template->renderInternal($this, 'backend/index', [
                'category_id' => $this->category_id,
                'key_path'    => is_array($this->tree_path_key) ? implode('/', $this->tree_path_key) : $this->tree_path_key,
                'grid_html'   => $grid_html
        ]);

    }
}
