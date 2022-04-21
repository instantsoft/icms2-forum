<?php
$this->setPageTitle(LANG_FORUM_MY_POSTS);
$this->setPageKeywords(LANG_FORUM_MY_POSTS);
$this->setPageDescription(LANG_FORUM_MY_POSTS);

$this->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));
$this->addBreadcrumb(LANG_FORUM_MY_POSTS);

if ($user->is_admin) {
    $this->addToolButton([
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', ['edit', 'forum']),
    ]);
}
?>

<h1><?php echo LANG_FORUM_MY_POSTS; ?></h1>

<?php
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
?>

<?php echo $list_html; ?>