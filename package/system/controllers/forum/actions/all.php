<?php

class actionForumAll extends cmsAction {

    public function run() {

        if(empty($this->options['show_ds_menu_index'])){
            return cmsCore::error404();
        }

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'all').'"/>');

        return $this->renderIndex();
    }

}
