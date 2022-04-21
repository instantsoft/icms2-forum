<?php

$user = cmsUser::getInstance();

if ($is_rss) {
    $this->addHead('<link rel="alternate" type="application/rss+xml" title="' . LANG_FORUM_RSS_THREADS . '" href="/rss/feed/forum?view=threads&category=1" />');
    $this->addHead('<link rel="alternate" type="application/rss+xml" title="' . LANG_FORUM_RSS_POSTS . '" href="/rss/feed/forum?view=posts&category=1" />');
}

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

<?php if ($is_rss && !empty($options['is_rss'])) { ?>
    <div class="forum_rss_icon">
        <a class="rss_icon" href="/rss/feed/forum?view=threads&category=1" title="<?php echo LANG_FORUM_RSS_THREADS; ?>"><?php echo LANG_FORUM_RSS_THREADS; ?></a>
        <a class="rss_icon" href="/rss/feed/forum?view=posts&category=1" title="<?php echo LANG_FORUM_RSS_POSTS; ?>"><?php echo LANG_FORUM_RSS_POSTS; ?></a>
    </div>
<?php } ?>

<h1><?php echo!empty($options['seo_h1']) ? $options['seo_h1'] : LANG_FORUM_FORUMS; ?></h1>

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