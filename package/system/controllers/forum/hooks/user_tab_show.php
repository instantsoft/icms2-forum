<?php

class onForumUserTabShow extends cmsAction {

    public function run($profile, $tab_name, $tab) {

        $this->model->filterEqual('user_id', $profile['id']);

        $page_url = href_to('users', $profile['id'], 'forum');

        $list_html = $this->renderForumPostsList($page_url);

        return $this->cms_template->renderInternal($this, 'profile_tab', [
            'tab'     => $tab,
            'user'    => $this->cms_user,
            'profile' => $profile,
            'html'    => $list_html
        ]);
    }

}
