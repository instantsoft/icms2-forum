<?php
$this->setPageTitle(($category['seo_title'] ? $category['seo_title'] : $category['title']));
$this->setPageKeywords(($category['seo_keys'] ? $category['seo_keys'] : string_get_meta_keywords($category['title'])));
$this->setPageDescription(($category['seo_desc'] ? $category['seo_desc'] : (!empty($category['description']) ? string_get_meta_description($category['description']) : $category['title'])));
$this->addHead('<link rel="canonical" href="'.href_to_abs('forum', $category['slug']).'"/>');

if ($is_can_add_thread) {
    $this->addToolButton(array(
        'class' => 'add',
        'icon' => 'plus-circle',
        'title' => LANG_FORUM_NEW_THREAD,
        'href'  => href_to('forum', 'thread_add', $category['id']),
    ));
}

if ($user->is_admin) {

    $this->addToolButton(array(
        'class' => 'folder_edit',
        'icon'  => 'pen-square',
        'title' => LANG_FORUM_CAT_EDIT,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum', 'category_edit', $category['id'])),
    ));

    $this->addToolButton(array(
        'class' => 'settings',
        'icon'  => 'wrench',
        'title' => LANG_FORUM_SETTINGS,
        'href'  => href_to('admin', 'controllers', array('edit', 'forum')),
    ));
}

?>

<h1 class="mb-3">
    <?php echo $category['title']; ?>
</h1>

<?php if (!empty($category['description']) || !empty($category['icon'])) { ?>

    <div class="category-view-description mb-3">
        <?php if (!empty($category['icon'])) { ?>
            <div class="category-view-icon">
                <?php echo html_image($category['icon'], 'small'); ?>
            </div>
        <?php } ?>
        <?php if (!empty($category['description'])) { ?>
            <div class="category-view-desc text-muted">
                <?php html($category['description']); ?>
            </div>
        <?php } ?>
    </div>

<?php } ?>
<?php if (empty($category['as_folder']) && ($is_can_add_thread || ($fix_threads_reads && $threads)) || $is_rss) { ?>
    <div class="container-fluid bg-primary border-bottom py-2">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <?php if ($is_can_add_thread) { ?>
                    <a class="d-inline-block text-decoration-none mr-3 text-white" href="<?php echo href_to('forum', 'thread_add', $category['id']); ?>">
                        <?php html_svg_icon('solid', 'plus-circle'); ?>
                        <?php echo LANG_FORUM_NEW_THREAD; ?>
                    </a>
                <?php } ?>

                <?php if ($fix_threads_reads && $threads) { ?>
                    <a class="d-inline-block text-decoration-none text-white" href="<?php echo href_to('forum', 'all_threads_view', $category['id']); ?>">
                        <?php html_svg_icon('solid', 'binoculars'); ?>
                        <?php echo LANG_FORUM_ALL_THREADS_VIEW; ?>
                    </a>
                <?php } ?>
            </div>
            <div class="col-auto ml-auto">
                <?php if ($is_rss){ ?>
                    <a class="text-warning text-decoration-none mr-3" href="<?php echo href_to('rss', 'feed', 'forum'); ?>?view=threads&category=<?php echo $category['id']; ?>">
                        <?php html_svg_icon('solid', 'rss'); ?>
                        <?php echo LANG_FORUM_RSS_THREADS; ?>
                    </a>
                    <a class="text-warning text-decoration-none" href="<?php echo href_to('rss', 'feed', 'forum'); ?>?view=posts&category=<?php echo $category['id']; ?>">
                        <?php html_svg_icon('solid', 'rss'); ?>
                        <?php echo LANG_FORUM_RSS_POSTS; ?>
                    </a>
                <?php } ?>
            </div>
        </div>

    </div>
<?php } ?>
<?php if (!empty($category['childs_path'])) { ?>

    <?php echo $this->renderChild('categories', array(
        'cats_list' => $category['childs_path'],
    )); ?>

<?php } ?>

<?php if (empty($category['as_folder'])) { ?>

    <?php if (!$threads) { ?>
        <div class="alert alert-info mt-4 alert-list-empty">
            <?php echo LANG_FORUM_NOT_THREADS_IN_FORUM; ?>
        </div>
    <?php } else { ?>

        <?php echo $this->renderChild('threads', [
            'user'    => $user,
            'threads' => $threads,
            'options' => $options
        ]); ?>

        <?php echo html_pagebar($page, $perpage, $total, href_to('forum', $category['slug'])); ?>

    <?php } ?>

<?php } ?>