<?php

$this->addHead('<link rel="canonical" href="'.href_to_abs('forum', 'latest_threads').'"/>');

$this->setPageTitle(!empty($options['menu_lthr']['seo']['title']) ? $options['menu_lthr']['seo']['title'] : LANG_FORUM_NEW_THREADS);
$this->setPageKeywords(!empty($options['menu_lthr']['seo']['keys']) ? $options['menu_lthr']['seo']['keys'] : LANG_FORUM_NEW_THREADS);
$this->setPageDescription(!empty($options['menu_lthr']['seo']['desc']) ? $options['menu_lthr']['seo']['desc'] : LANG_FORUM_NEW_THREADS);

$this->addBreadcrumb(!empty($options['menu_lthr']['seo']['h1']) ? $options['menu_lthr']['seo']['h1'] : LANG_FORUM_NEW_THREADS);

if ($user->is_admin) {
    $this->addToolButton([
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', ['edit', 'forum']),
    ]);
}
?>

<h1><?php echo !empty($options['menu_lthr']['seo']['h1']) ? $options['menu_lthr']['seo']['h1'] : LANG_FORUM_NEW_THREADS; ?></h1>

<?php
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
?>

<?php if (!$threads) { ?>
    <div class="alert alert-info mt-4 alert-list-empty">
        <?php echo LANG_LIST_EMPTY; ?>
    </div>
<?php return; } ?>

<?php echo $this->renderChild('threads', [
    'user'    => $user,
    'threads' => $threads,
    'options' => $options
]); ?>

<?php echo html_pagebar($page, $perpage, $total); ?>