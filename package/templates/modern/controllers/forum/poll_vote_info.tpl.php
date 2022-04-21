<?php if (!$votes){ ?>
    <p class="alert alert-info m-0"><?php echo LANG_FORUM_POLL_NO_VOICE; ?></p>
<?php } ?>

<?php if ($votes){ ?>

    <div class="mt-n3" id="icms-forum-poll-info-list">
        <?php $this->renderChild('poll_vote_info_list', ['votes' => $votes]); ?>
    </div>

    <?php if ($pages > 1){ ?>
        <div class="mt-3" data-url="<?php echo $this->href_to('poll_vote_info', [$poll['id'], $answer_id]); ?>" id="icms-forum-poll-info-pagination">
            <?php for($p=1; $p<=$pages; $p++){ ?>
                <a href="#<?php echo $p; ?>" onclick="return icms.forum.loadPollInfo(this);" data-page="<?php echo $p; ?>" class="btn btn-primary btn-sm<?php if ($p==$page) { ?> active<?php } ?>">
                    <span><?php echo $p; ?></span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

<?php } ?>