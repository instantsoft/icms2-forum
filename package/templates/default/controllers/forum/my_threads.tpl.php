<?php

$user = cmsUser::getInstance();

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

$odd = 0;
$is_odd = false;

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

<table class="threads-sorting-table" cellspacing="0" cellpadding="0">

    <tr>

        <td class="threads-sorting-title"><?php echo LANG_FORUM_OPTIONS_VIEW; ?></td>

    </tr>

    <tr>

        <td class="threads-sorting">

            <form action="" method="get">

                <table>

                    <tr>

                        <td>

                            <label><?php echo LANG_FORUM_THREAD_ORDER; ?></label>

                            <select class="form-control" name="order_by">
                                <option value="title"<?php if ($order_by == 'title') { ?> selected="selected"<?php } ?>><?php echo LANG_TITLE; ?></option>
                                <option value="date_pub"<?php if ($order_by == 'date_pub') { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_MODIFY_DATE; ?></option>
                                <option value="posts_count"<?php if ($order_by == 'posts_count') { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_ANSWER_COUNT; ?></option>
                                <option value="hits"<?php if ($order_by == 'hits') { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_HITS_COUNT; ?></option>
                            </select>

                        </td>

                        <td>

                            <label><?php echo LANG_FORUM_ORDER_TO; ?></label>

                            <select class="form-control" name="order_to">
                                <option value="asc"<?php if ($order_to == 'asc') { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_ORDER_ASC; ?></option>
                                <option value="desc"<?php if ($order_to == 'desc') { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_ORDER_DESC; ?></option>
                            </select>

                        </td>

                        <td>

                            <label><?php echo LANG_SHOW; ?></label>

                            <select class="form-control" name="daysprune">
                                <option value="0"<?php if (!$daysprune) { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_SHOW_ALL; ?></option>
                                <option value="1"<?php if ($daysprune == 1) { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_SHOW_DAY; ?></option>
                                <option value="7"<?php if ($daysprune == 7) { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_SHOW_W; ?></option>
                                <option value="30"<?php if ($daysprune == 30) { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_SHOW_MONTH; ?></option>
                                <option value="365"<?php if ($daysprune == 365) { ?> selected="selected"<?php } ?>><?php echo LANG_FORUM_SHOW_YEAR; ?></option>
                            </select>

                        </td>

                    </tr>

                    <tr>

                        <td colspan="3">

                            <?php echo html_submit(LANG_FORUM_SHOW_THREADS); ?>

                        </td>

                    </tr>

                </table>

            </form>

        </td>

    </tr>

</table>

<?php if (!$threads) { ?>
    <div class="alert alert-info alert-list-empty">
        <?php echo LANG_FORUM_NOT_THREADS_IN_FORUM; ?>
    </div>
<?php } else { ?>

    <?php echo $this->renderChild('threads', [
        'user'    => $user,
        'threads' => $threads,
        'options' => $options,
        'is_can_add_thread' => false
    ]); ?>

    <?php echo html_pagebar($page, $perpage, $total, href_to('forum', 'my_threads'), array('order_by' => $order_by, 'order_to' => $order_to, 'daysprune' => $daysprune)); ?>

<?php } ?>
