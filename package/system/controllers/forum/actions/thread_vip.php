<?php
/**
 * Выделение темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThreadVip extends cmsAction {

    public function run($thread_id) {

        // Доступно авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем данные по теме
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администратору сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и теме
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        if (!$this->thread_access->is_can_thread_vip) {
            return cmsCore::error404();
        }

        // Форма выделения темы
        $form = $this->getForm('thread_vip');

        // Если форма была отправлена
        if ($this->request->has('submit')) {

            // Получаем данные из запроса
            $request = $form->parse($this->request, true);

            // Проверяем запрошенные данные
            $errors = $form->validate($this, $request);

            // Если ошибок нет, выделяем тему
            if (!$errors) {

                if (!$request['is_vip']) {
                    $request['vip_expires'] = null;
                }

                // Обновляем тему
                $this->model->updateThread($thread['id'], $request);

                // Сообщаем об успешности выделения темы
                cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_EDITED, 'success');

                // Возвращаем пользователя на страницу темы
                $this->redirectTo('forum', $thread['slug'] . '.html');
            }

            // Если есть ошибки, сообщаем о них
            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        return $this->cms_template->render('thread_vip', [
            'thread'   => $thread,
            'category' => $category,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
