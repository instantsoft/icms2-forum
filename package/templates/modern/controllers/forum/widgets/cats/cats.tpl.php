<ul class="icms-content-subcats list-unstyled my-n2">

    <?php
    $last_level = 0;
    $is_first_level = current($cats)['ns_level'];
    $is_visible = false;
    ?>

    <?php foreach ($cats as $cat) { ?>

        <?php

        $is_active = (!empty($active_cat['id']) && $cat['id'] == $active_cat['id']);
        $is_visible = isset($path[$cat['id']]) || isset($path[$cat['parent_id']]) || $cat['ns_level'] <= $is_first_level;
        if (!isset($cat['ns_level'])) {
            $cat['ns_level'] = 1;
        }
        $cat['childs_count'] = ($cat['ns_right'] - $cat['ns_left']) > 1;
        $url = href_to('forum', $cat['slug']);

        ?>

        <?php for ($i = 0; $i < ($last_level - $cat['ns_level']); $i++) { ?>
            </li></ul>
        <?php } ?>

<?php if ($cat['ns_level'] <= $last_level) { ?>
    </li>
<?php } ?>

<?php

$css_classes = ['my-2'];
if ($is_active) {
    $css_classes[] = 'text-dark';
}
if (!$is_visible && !$show_full_tree) {
    $css_classes[] = 'd-none';
}
?>

<li <?php if ($css_classes) { ?>class="<?php echo implode(' ', $css_classes); ?>"<?php } ?>>

    <a href="<?php echo $url; ?>">
        <?php if ($cat['as_folder']) { ?>
            <span class="text-warning"><?php html_svg_icon('solid', 'folder'); ?></span>
        <?php } else { ?>
            <?php html_svg_icon('solid', 'file'); ?>
        <?php } ?>
        <?php html($cat['title']); ?>
    </a>

<?php if ($cat['childs_count']) { ?><ul class="list-unstyled pl-3"><?php } ?>

<?php $last_level = $cat['ns_level']; ?>

<?php } ?>

<?php for ($i = 0; $i < $last_level; $i++) { ?>
</li></ul>
<?php } ?>