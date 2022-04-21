<?php

class backendForum extends cmsBackend {

    public $useDefaultOptionsAction     = true;
    public $useDefaultPermissionsAction = true;

    public function getBackendMenu() {
        return [
            [
                'title' => LANG_FORUM_FORUMS,
                'url'   => href_to($this->root_url),
                'options' => [
                    'icon' => 'folder-open'
                ]
            ],
            [
                'title' => LANG_PERMISSIONS,
                'url'   => href_to($this->root_url, 'perms', 'forum'),
                'options' => [
                    'icon' => 'key'
                ]
            ],
            [
                'title' => LANG_OPTIONS,
                'url'   => href_to($this->root_url, 'options'),
                'options' => [
                    'icon' => 'cog'
                ]
            ]
        ];
    }

}
