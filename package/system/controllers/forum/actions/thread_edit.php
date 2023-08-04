<?php
/**
 * Редактирование темы
 *
 * @property \forum $cat_access
 * @property \forum $thread_access
 * @property \modelForum $model
 */
class actionForumThreadEdit extends cmsAction {

    public function run($thread_id) {

        // Доступно только авторизованным пользователям
        if (!$this->cms_user->is_logged) {
            return cmsCore::error404();
        }

        // Получаем данные по теме форума
        $thread = $this->model->getThreadByField($thread_id);
        if (!$thread) {
            return cmsCore::error404();
        }

        // Предварительно удаленные темы, доступны только администраторам сайта
        if (!empty($thread['is_deleted']) && !$this->cms_user->is_admin) {
            return cmsCore::error404();
        }

        // Получаем данные по разделу
        $category = $this->model->getCategoryByField($thread['category_id']);
        if (!$category) {
            return cmsCore::error404();
        }

        // Загружаем доступ к разделу и доступы для темы
        $this->loadCatAccess($category['path'])->loadThreadAccess($thread);

        if (!$this->thread_access->is_can_thread_edit) {
            return cmsCore::error404();
        }

        // Получаем прикрепленный опрос, если такой есть
        $thread['poll'] = $this->model->getThreadPoll($thread['id'], $this->cms_user);

        // Форма редактирования темы
        $form = $this->getForm('thread', ['edit', $this->cat_access->is_moder]);

        // Добавляем голосование, если разрешено
        if($this->thread_access->is_can_poll_add){

            $poll_fieldsets = $this->getThreadPollFormFieldsets(['do' => 'edit', 'poll' => $thread['poll']]);

            foreach ($poll_fieldsets as $fid => $poll_fieldset) {
                $form->addFieldset('', $fid, $poll_fieldset);
            }
        }

        // Обработка формы событиями
        list($form, $thread) = cmsEventsManager::hook("forum_thread_form", [$form, $thread]);

        // Запоминаем старые данные
        $thread_old = $thread;

        // Если форма отправлена
        if ($this->request->has('submit')) {

            // Получаем данные из формы
            $thread = array_replace_recursive($thread, $form->parse($this->request, true, $thread));

            // Проверяем полученные данные
            $errors = $form->validate($this, $thread);

            // Если ошибок нет и поле названия темы не пустое
            if (!$errors) {

                // Если тема переносится в другой раздел, ставим метку откуда переносится
                if ($thread['category_id'] != $thread_old['category_id']) {
                    $thread['from_cat']    = $thread_old['category_id'];
                    $thread['category_id'] = $thread['category_id'];
                }

                // Если установлено, меняем URL темы
                if (!empty($thread['change_url'])) {
                    $thread['category'] = $category;
                    $thread['url_pattern'] = $this->options['item_url_pattern'];
                    $thread['slug'] = $this->model->getThreadSlug($thread);
                }

                // Обновляем запись в ленте активности
                cmsCore::getController('activity')->updateEntry('forum', 'add.thread', $thread['id'], array(
                    'subject_title' => $thread['title'],
                    'subject_id'    => $thread['id'],
                    'subject_url'   => href_to_rel('forum', $thread['slug'] . '.html')
                ));

                // Изменяем тему
                $this->model->updateThread($thread['id'], $thread, $category);

                // Уведосляем пользователя об успешности изменения темы
                cmsUser::addSessionMessage(LANG_FORUM_THREAD_IS_EDITED, 'success');

                // Возвращаем пользователя обратно в тему
                $this->redirectTo('forum', $thread['slug'] . '.html');
            }

            // Если есть ошибки, сообщаем об этом
            if ($errors) {
                cmsUser::addSessionMessage(LANG_FORM_ERRORS, 'error');
            }
        }

        $this->cms_template->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));

        if (!empty($category['path'])) {
            foreach ($category['path'] as $c) {
                $this->cms_template->addBreadcrumb($c['title'], href_to('forum', $c['slug']));
            }
        }

        return $this->cms_template->render('thread_add', [
            'do'        => 'edit',
            'page_title' => LANG_FORUM_THREAD_EDIT,
            'thread'   => $thread,
            'category' => $category,
            'form'     => $form,
            'errors'   => isset($errors) ? $errors : false
        ]);
    }

}
