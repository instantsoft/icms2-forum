<?php $user = cmsUser::getInstance(); ?>

<div style="padding: 20px;">

    <form action="<?php echo href_to('forum', 'post_move', array($post['id'])); ?>" method="post">

        <fieldset id="fset_move">

            <legend><?php echo LANG_FORUM_MOVE_POST_IN_THREAD; ?></legend>

            <div id="f_thread_id" class="form-group field ft_list">

                <select id="thread_id" class="form-control" name="thread_id">

                    <?php foreach ($threads_tree as $id => $category) { ?>

                        <optgroup label="<?php echo str_repeat('-- ', $category['level']); ?><?php html($category['cat_title']); ?>">
                            <?php foreach ($category['threads'] as $id => $item) { ?>
                                <option value="<?php echo $item['thread_id']; ?>"<?php if ($thread['id'] == $item['thread_id']) { ?> selected="selected"<?php } ?>><?php html($item['thread_title']); ?></option>
                            <?php } ?>

                        </optgroup>

                    <?php } ?>
                </select>

            </div>

        </fieldset>

        <?php echo html_submit(); ?>

    </form>

</div>
