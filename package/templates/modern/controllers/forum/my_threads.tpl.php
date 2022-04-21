<?php

$this->setPageTitle(LANG_FORUM_MY_THREADS);
$this->setPageKeywords(LANG_FORUM_MY_THREADS);
$this->setPageDescription(LANG_FORUM_MY_THREADS);

if ($user->id && !empty($options['preview_thread'])) {
    $this->addTplCSSNameFromContext('jBox');
    $this->addTplJSNameFromContext('jBox');
}

$this->addBreadcrumb(LANG_FORUM_FORUMS, href_to('forum'));
$this->addBreadcrumb(LANG_FORUM_MY_THREADS);

if ($user->is_admin) {
    $this->addToolButton(array(
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum')),
    ));
}

?>

<h1><?php echo LANG_FORUM_MY_THREADS; ?></h1>

<?php
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
?>
<form action="" method="get" class="mb-4">
    <div class="form-row">
        <div class="form-group col-sm-6 col-lg-4">
            <label for="order_by"><?php echo LANG_FORUM_THREAD_ORDER; ?></label>
            <?php echo html_select('order_by', $filter['order_by'], $order_by); ?>
        </div>
        <div class="form-group col-sm-6 col-lg-4">
            <label for="order_to"><?php echo LANG_FORUM_ORDER_TO; ?></label>
            <?php echo html_select('order_to', $filter['order_to'], $order_to); ?>
        </div>
        <div class="form-group col-sm-12 col-lg-4">
            <label for="daysprune"><?php echo LANG_SHOW; ?></label>
            <?php echo html_select('daysprune', $filter['daysprune'], $daysprune); ?>
        </div>
    </div>
    <?php echo html_submit(LANG_FORUM_SHOW_THREADS); ?>
</form>

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

<?php echo html_pagebar($page, $perpage, $total, href_to('forum', 'my_threads'), ['order_by' => $order_by, 'order_to' => $order_to, 'daysprune' => $daysprune]); ?>