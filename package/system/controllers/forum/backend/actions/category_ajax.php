<?php
/**
 * Список разделов форума
 */
class actionForumCategoryAjax extends cmsAction {

    public function run($category_id = false) {

        if (!$category_id) {
            cmsCore::error404();
        }
        if (!$this->request->isAjax()) {
            cmsCore::error404();
        }

        $category = $this->model->getCategoryByField($category_id);
        if (!$category) {
            cmsCore::error404();
        }

        $grid = $this->loadDataGrid('cats', [$category]);

        $filter     = array();
        $filter_str = $this->request->get('filter', '');

        $filter_str = cmsUser::getUPSActual('admin.grid_filter.forum', $filter_str);

        if ($filter_str) {

            parse_str($filter_str, $filter);

            $this->model->applyGridFilter($grid, $filter);

            $grid['filter'] = $filter;
        }

        $this->model->filterGt('ns_left', $category['ns_left']);
        $this->model->filterLt('ns_right', $category['ns_right']);

        $this->model->filterGt('parent_id', 0);

        $total = $this->model->getCount('forum_cats');

        $perpage = isset($filter['perpage']) ? $filter['perpage'] : admin::perpage;

        $pages = ceil($total / $perpage);

        $this->model->setPerPage($perpage);

        $items = $this->model->getCategoriesTree('forum');

        $this->cms_template->renderGridRowsJSON($grid, $items, $total, $pages);

        $this->halt();
    }

}
