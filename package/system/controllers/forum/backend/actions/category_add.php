<?php
/**
 * Создание раздела форума
 */
class actionForumCategoryAdd extends cmsAction {

    public function run($parent_id = false) {

        $category = array('parent_id' => $parent_id);

        $form = $this->getForm('category', array('add'));

        if ($this->request->has('submit')) {

            $category = $form->parse($this->request, true);

            $errors = $form->validate($this, $category);

            if (!$errors) {

                $category = $this->model->addCategory('forum', $category);

                cmsUser::addSessionMessage(LANG_CP_FORUM_CAT_CREATED, 'success');

                $this->redirectToAction('');
            }

            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('backend/category_form', array(
            'do'       => 'add',
            'form'     => $form,
            'category' => $category,
            'errors'   => isset($errors) ? $errors : false
        ));
    }

}
