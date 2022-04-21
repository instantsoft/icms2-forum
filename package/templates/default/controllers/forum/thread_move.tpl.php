<fieldset id="fset_move">

    <legend><?php echo LANG_FORUM_MOVE_THREAD_IN_FORUM; ?></legend>

    <div id="f_category_id" class="form-group field ft_list">

        <select id="category_id" class="form-control" name="category_id">
            <?php foreach ($cats_tree as $id => $cat) { ?>

                <?php if (!empty($cat['as_folder'])) { ?>
                    <optgroup label="<?php html($cat['title']); ?>"><?php html($cat['title']); ?></optgroup>
                <?php } else { ?>
                    <option value="<?php echo $cat['id']; ?>"<?php if ($category['id'] == $cat['id']) { ?> selected="selected"<?php } ?>><?php html($cat['title']); ?></option>
                <?php } ?>

            <?php } ?>
        </select>

    </div>

</fieldset>
