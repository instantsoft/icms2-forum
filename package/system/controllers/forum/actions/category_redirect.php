<?php
/**
 * Переадресация с URL страниц форума InstantCMS v. 1.10...
 */
class actionForumCategoryRedirect extends cmsAction {

    public function run() {

        $category_id = $this->request->get('category_id', 0);
        $page = $this->request->get('page', 1);

        if (!$category_id) {
            return cmsCore::error404();
        }

        // Получаем данные по теме форума
        $category = $this->model->getCategoryByField($category_id);
        if (!$category) {
            return cmsCore::error404();
        }

        // Перенаправляем на новый адрес
        return $this->redirect(href_to('forum', $category['slug'] . ($page > 1 ? '?page=' . $page : '')), 301);
    }

}
