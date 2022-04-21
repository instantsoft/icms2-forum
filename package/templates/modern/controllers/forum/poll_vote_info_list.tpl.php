<?php foreach($votes as $vote){ ?>
    <div class="item d-flex align-items-center mt-3">
        <a href="<?php echo href_to_profile($vote['user']); ?>" class="icms-user-avatar mr-3">
        <?php if($vote['user']['avatar']){ ?>
            <?php echo html_avatar_image($vote['user']['avatar'], 'micro', $vote['user']['nickname']); ?>
        <?php } else { ?>
            <?php echo html_avatar_image_empty($vote['user']['nickname'], 'avatar__mini'); ?>
        <?php } ?>
        </a>
        <a href="<?php echo href_to_profile($vote['user']); ?>">
            <?php html($vote['user']['nickname']); ?>
        </a>
        <span class="m-0 ml-auto">
            <?php echo html_date_time($vote['date_pub']); ?>
        </span>
    </div>
<?php } ?>