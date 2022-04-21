<?php
/**
 * Редактирование раздела
 */
class actionForumCategoryEdit extends cmsAction {

    public function run($category_id = false) {

        if (!$category_id) { cmsCore::error404(); }

        $category = $this->model->localizedOff()->getCategoryByField($category_id);
        if (!$category) { cmsCore::error404(); }

        $this->model->localizedRestore();

        $form = $this->getForm('category', array('edit'));

        if ($this->request->has('submit')) {

            $category = array_merge($category, $form->parse($this->request, true));

            $errors = $form->validate($this, $category);

            if (!$errors) {

                $this->model->updateCategory('forum', $category_id, $category);

                cmsUser::addSessionMessage(LANG_CP_FORUM_CAT_UPDATED, 'success');

                $this->redirectToAction('index');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/category_form', array(
            'do'       => 'edit',
            'form'     => $form,
            'category' => $category,
            'errors'   => isset($errors) ? $errors : false
        ));
    }

}
