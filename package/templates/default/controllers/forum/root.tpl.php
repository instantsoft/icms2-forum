<?php
$this->setPageTitle(!empty($options['menu_index']['seo']['title']) ? $options['menu_index']['seo']['title'] : LANG_FORUM_FORUMS_ACTIVITY);
$this->setPageKeywords(!empty($options['menu_index']['seo']['keys']) ? $options['menu_index']['seo']['keys'] : LANG_FORUM_FORUMS);
$this->setPageDescription(!empty($options['menu_index']['seo']['desc']) ? $options['menu_index']['seo']['desc'] : LANG_FORUM_FORUMS);

$this->addBreadcrumb(!empty($options['menu_index']['seo']['h1']) ? $options['menu_index']['seo']['h1'] : LANG_FORUM_FORUMS_ACTIVITY);

if ($user->is_admin) {
    $this->addToolButton(array(
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', ['edit', 'forum']),
    ));
}
?>

<h1>
    <?php echo !empty($options['menu_index']['seo']['h1']) ? $options['menu_index']['seo']['h1'] : LANG_FORUM_FORUMS; ?>
</h1>

<?php
    $this->renderAsset('ui/datasets-panel', [
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ]);
?>