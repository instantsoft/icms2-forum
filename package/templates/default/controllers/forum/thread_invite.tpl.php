<?php

$user = cmsUser::getInstance();

$this->setPageTitle(LANG_FORUM_THREAD_INVITE);
$this->setPageDescription(LANG_FORUM_THREAD_INVITE);
$this->setPageKeywords(LANG_FORUM_THREAD_INVITE);

if ($user->id) {

    $this->addToolButton(array(
        'class' => 'my-threads',
        'icon'  => 'address-book',
        'title' => LANG_FORUM_MY_THREADS,
        'href'  => href_to('forum', 'my_threads'),
    ));

    $this->addToolButton(array(
        'class' => 'my-posts',
        'icon'  => 'address-card',
        'title' => LANG_FORUM_MY_POSTS,
        'href'  => href_to('forum', 'my_posts'),
    ));

    $this->addToolButton(array(
        'class' => 'threads',
        'icon'  => 'newspaper',
        'title' => LANG_FORUM_NEW_THREADS,
        'href'  => href_to('forum', 'latest_threads'),
    ));

    $this->addToolButton(array(
        'class' => 'posts',
        'icon'  => 'file-alt',
        'title' => LANG_FORUM_LATEST_POSTS,
        'href'  => href_to('forum', 'latest_posts'),
    ));
}

?>

<h1><?php echo LANG_FORUM_THREAD_INVITE . ': ' . $thread['title']; ?></h1>

<div class="modal-padding">

    <form id="thread_invite" action="" method="post">

        <fieldset>

            <legend><?php echo LANG_FILTER; ?></legend>

            <table cellpadding="0" cellspacing="0" border="0" width="100%">

                <?php $index = 0; ?>

                <?php foreach ($fields as $field) { ?>

                    <?php if ($field['handler']->filter_type == false) { continue; } ?>
                    <?php if ($field['name'] == 'user') { $field['name'] = 'user_id'; } ?>

                    <tr>
                        <td>
                            <label><?php html($field['title']); ?></label>
                            <?php echo html_input('hidden', "filters[{$index}][field]", $field['name']); ?>
                        </td>
                        <td>
                            <?php if ($field['handler']->filter_type == 'int') { ?>
                                <select class="form-control" name="filters[<?php echo $index; ?>][condition]">
                                    <option value="eq"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'eq' ? ' selected' : ''; ?>>=</option>
                                    <option value="gt"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'gt' ? ' selected' : ''; ?>>&gt;</option>
                                    <option value="lt"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'lt' ? ' selected' : ''; ?>>&lt;</option>
                                    <option value="ge"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'ge' ? ' selected' : ''; ?>>&ge;</option>
                                    <option value="le"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'le' ? ' selected' : ''; ?>>&le;</option>
                                    <option value="nn"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'nn' ? ' selected' : ''; ?>><?php echo LANG_FILTER_NOT_NULL; ?></option>
                                    <option value="ni"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'ni' ? ' selected' : ''; ?>><?php echo LANG_FILTER_IS_NULL; ?></option>
                                </select>
                            <?php } ?>

                            <?php if ($field['handler']->filter_type == 'str') { ?>
                                <select class="form-control" name="filters[<?php echo $index; ?>][condition]">
                                    <option value="lk"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'lk' ? ' selected' : ''; ?>><?php echo LANG_FILTER_LIKE; ?></option>
                                    <option value="eq"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'eq' ? ' selected' : ''; ?>>=</option>
                                    <option value="lb"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'lb' ? ' selected' : ''; ?>><?php echo LANG_FILTER_LIKE_BEGIN; ?></option>
                                    <option value="lf"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'lf' ? ' selected' : ''; ?>><?php echo LANG_FILTER_LIKE_END; ?></option>
                                    <option value="nn"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'nn' ? ' selected' : ''; ?>><?php echo LANG_FILTER_NOT_NULL; ?></option>
                                    <option value="ni"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'ni' ? ' selected' : ''; ?>><?php echo LANG_FILTER_IS_NULL; ?></option>
                                </select>
                            <?php } ?>

                            <?php if ($field['handler']->filter_type == 'date') { ?>
                                <select class="form-control" name="filters[<?php echo $index; ?>][condition]">
                                    <option value="eq"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'eq' ? ' selected' : ''; ?>>=</option>
                                    <option value="gt"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'gt' ? ' selected' : ''; ?>>&gt;</option>
                                    <option value="lt"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'lt' ? ' selected' : ''; ?>>&lt;</option>
                                    <option value="ge"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'ge' ? ' selected' : ''; ?>>&ge;</option>
                                    <option value="le"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'le' ? ' selected' : ''; ?>>&le;</option>
                                    <option value="dy"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'dy' ? ' selected' : ''; ?>><?php echo LANG_FILTER_DATE_YOUNGER; ?></option>
                                    <option value="do"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'do' ? ' selected' : ''; ?>><?php echo LANG_FILTER_DATE_OLDER; ?></option>
                                    <option value="nn"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'nn' ? ' selected' : ''; ?>><?php echo LANG_FILTER_NOT_NULL; ?></option>
                                    <option value="ni"<?php echo!empty($filters[$index]['condition']) && $filters[$index]['condition'] == 'ni' ? ' selected' : ''; ?>><?php echo LANG_FILTER_IS_NULL; ?></option>
                                </select>
                            <?php } ?>

                        </td>
                            <?php if (!empty($field['type']) && $field['type'] == 'city') { ?>
                            <td>
                            <?php $field['handler']->setName("filters:{$index}:value"); ?>
                            <?php echo $field['handler']->getInput(!empty($filters[$index]['value']) ? $filters[$index]['value'] : ''); ?>
                                <?php ob_start(); ?>
                                <script>$( '#geo-widget-filters_<?php echo $index; ?>_value' ).prev( 'label' ).remove();</script>
                                <?php $this->addBottom(ob_get_clean()); ?>
                            </td>

                            <?php } else { ?>

                            <td>
                                <?php

                                $attr = ($field['handler']->filter_hint) ? array('placeholder' => $field['handler']->filter_hint) : null;
                                echo html_input('text', "filters[{$index}][value]", (!empty($filters[$index]['value']) ? $filters[$index]['value'] : ''), $attr);

                                ?>
                            </td>

                    <?php } ?>

                    </tr>

    <?php $index++; ?>

        <?php } ?>

            </table>

        </fieldset>

        <div class="buttons">
            <?php echo html_submit(LANG_FORUM_THREAD_INVITE); ?>
            <?php echo html_button(LANG_CANCEL, 'cancel', "location.href='{$cancel_href}'", array('class' => 'button-cancel btn-secondary')); ?>
        </div>

    </form>

</div>