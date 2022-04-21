<?php

class onForumUserDelete extends cmsAction {

    public function run($user) {

        $this->model->deleteUserPosts($user['id']);

        return $user;
    }

}
