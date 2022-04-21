<?php

$this->setPageTitle(!empty($options['seo_title']) ? $options['seo_title'] : LANG_FORUM_FORUMS);
$this->setPageKeywords(!empty($options['seo_keys']) ? $options['seo_keys'] : LANG_FORUM_FORUMS);
$this->setPageDescription(!empty($options['seo_desc']) ? $options['seo_desc'] : LANG_FORUM_FORUMS);

$this->addBreadcrumb(!empty($options['seo_H1']) ? $options['seo_H1'] : LANG_FORUM_FORUMS);

if ($user->is_admin) {
    $this->addToolButton(array(
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum')),
    ));
}
?>

<h1>
    <?php echo !empty($options['seo_h1']) ? $options['seo_h1'] : LANG_FORUM_FORUMS; ?>
    <?php if ($is_rss){ ?>
        <sup>
            <a class="inline_rss_icon d-none d-lg-inline-block" title="RSS: <?php echo LANG_FORUM_RSS_THREADS; ?>" href="<?php echo href_to('rss', 'feed', 'forum'); ?>?view=threads&category=1" data-toggle="tooltip" data-placement="top">
                <?php html_svg_icon('solid', 'rss'); ?>
            </a>
            <a class="inline_rss_icon d-none d-lg-inline-block" title="RSS: <?php echo LANG_FORUM_RSS_POSTS; ?>" href="<?php echo href_to('rss', 'feed', 'forum'); ?>?view=posts&category=1" data-toggle="tooltip" data-placement="top">
                <?php html_svg_icon('solid', 'rss'); ?>
            </a>
        </sup>
    <?php } ?>
</h1>

<?php
    $this->renderAsset('ui/datasets-panel', array(
        'datasets'        => $datasets,
        'dataset_name'    => $dataset_name,
        'current_dataset' => $dataset,
        'base_ds_url'     => $base_ds_url
    ));
?>

<?php if (!$cats_list) { return; } ?>

<?php echo $this->renderChild('categories', array(
    'cats_list' => $cats_list,
)); ?>