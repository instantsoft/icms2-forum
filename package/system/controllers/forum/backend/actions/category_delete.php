<?php
/**
 * Удаление раздела форума
 * @property \modelForum $model
 */
class actionForumCategoryDelete extends cmsAction {

    public function run($category_id){

        if (!$category_id || $category_id == 1) { cmsCore::error404(); }

        $csrf_token = $this->request->get('csrf_token', '');
        if (!cmsForm::validateCSRFToken( $csrf_token )){
            cmsCore::error404();
        }

        $category = $this->model->getCategoryByField($category_id);
        if (!$category) { cmsCore::error404(); }

        // Обновляем куки на родительский раздел
        $cookie_path = cmsUser::getCookie('forum_tree_path');
        $cookie_path = explode('/', $cookie_path);
        array_pop($cookie_path);
        $cookie_path = implode('/', $cookie_path);

        cmsUser::setCookiePublic('forum_tree_path', $cookie_path);

        $threads = $this->model->getCatThreads($category);

        if ($threads){
            foreach ($threads as $thread) {
                $this->model->deleteThread($thread, true);
            }
            // Обновляем количество и последнее сообщение в родительских разделах
            $this->model->updateLastPostAfterThreadEdit($category);
        }

        $this->model->deleteCategory('forum', $category['id']);

        $this->redirectToAction('index');
    }

}
