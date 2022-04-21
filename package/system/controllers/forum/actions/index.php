<?php

class actionForumIndex extends cmsAction {

    public function run() {

        $this->cms_template->addHead('<link rel="canonical" href="'.href_to_abs('forum').'"/>');

        if(empty($this->options['show_ds_menu_index'])){
            return $this->renderIndex();
        }

        $datasets = $this->getDatasets();

        $dataset_name = 'index';

        $dataset = isset($datasets[$dataset_name]) ? $datasets[$dataset_name] : [];

        return $this->cms_template->render('root', [
            'base_ds_url'  => href_to($this->name) . '%s',
            'datasets'     => $datasets,
            'dataset_name' => $dataset_name,
            'dataset'      => $dataset,
            'user'         => $this->cms_user,
            'options'      => $this->options
        ]);
    }

}
