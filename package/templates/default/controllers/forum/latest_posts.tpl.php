<?php

$this->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'latest_posts').'"/>');

$this->setPageTitle(!empty($options['menu_lp']['seo']['title']) ? $options['menu_lp']['seo']['title'] : LANG_FORUM_LATEST_POSTS);
$this->setPageKeywords(!empty($options['menu_lp']['seo']['keys']) ? $options['menu_lp']['seo']['keys'] : LANG_FORUM_LATEST_POSTS);
$this->setPageDescription(!empty($options['menu_lp']['seo']['desc']) ? $options['menu_lp']['seo']['desc'] : LANG_FORUM_LATEST_POSTS);

$this->addBreadcrumb(!empty($options['menu_lp']['seo']['h1']) ? $options['menu_lp']['seo']['h1'] : LANG_FORUM_LATEST_POSTS);

if ($user->is_admin) {
    $this->addToolButton([
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', ['edit', 'forum']),
    ]);
}
?>

<h1><?php echo !empty($options['menu_lp']['seo']['h1']) ? $options['menu_lp']['seo']['h1'] : LANG_FORUM_LATEST_POSTS; ?></h1>

<?php
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
?>

<?php echo $list_html; ?>
