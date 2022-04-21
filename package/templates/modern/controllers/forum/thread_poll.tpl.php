<div id="icms-thread-poll" class="icms-forum__thread-poll mb-3 mb-md-4" data-vote_url="<?php echo href_to('forum', 'poll_vote'); ?>" data-poll_url="<?php echo href_to('forum', 'poll', $thread['id']); ?>" data-poll_delete_url="<?php echo href_to('forum', 'poll_delete', $thread['id']); ?>" data-confirm_delete="<?php html(LANG_FORUM_CONFIRM_DELETE_POLL); ?>">

    <h4>
        <?php html_svg_icon('solid', 'poll'); ?>
        <?php echo $thread_poll['title']; ?>
    </h4>
<?php if ($thread_poll['date_pub_end'] && !$thread_poll['is_closed']) { ?>
    <div class="text-muted small mb-3">
        <b><?php echo LANG_FORUM_END_DATE_POLL; ?></b>
        <?php echo mb_strtolower(html_date($thread_poll['date_pub_end'], true)); ?>.
    </div>
<?php } ?>
<?php if (!empty($thread_poll['description'])) { ?>
    <p>
        <?php echo $thread_poll['description']; ?>
    </p>
<?php } ?>

    <div class="row">
        <div class="col-lg-6">
            <?php if ($show_result) { ?>

                <div class="icms-forum__thread-poll_results">
                <?php foreach ($thread_poll['answers'] as $answer_id => $questions) { ?>
                    <?php
                    $is_my_answer = in_array($answer_id, $thread_poll['user_answer_ids']);
                    ?>
                    <div class="icms-forum__thread-poll_answer">
                        <div class="text-muted small font-weight-bold">
                            <?php echo $questions; ?>
                            <?php if ($is_my_answer && $thread_poll['options']['change'] && !$thread_poll['is_closed']) { ?>
                                <a class="ml-2" href="#" onclick="return icms.forum.loadPoll('revote');" title="<?php echo LANG_FORUM_CHANGE_VOTE; ?>">
                                    <?php html_svg_icon('solid', 'pen'); ?>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="progress"<?php if($is_my_answer){ ?> title="<?php echo LANG_FORUM_YOUR_ANSWER; ?>" data-toggle="tooltip" data-placement="right"<?php } ?>>
                            <div class="progress-bar icms-links-inherit-color<?php if($is_my_answer){ ?> bg-success<?php } ?>"<?php if (!empty($thread_poll['results']['percents'][$answer_id])){ ?> style="width:<?php echo $thread_poll['results']['percents'][$answer_id]; ?>%"<?php } ?>>
                                <?php if ($thread_poll['options']['answers_is_pub'] && $thread_poll['results']['answers'][$answer_id]) { ?>
                                    <a href="#" class="text-decoration-none" title="<?php html(sprintf(LANG_FORUM_POLL_PRESS_FOR_DETAIL, $questions)); ?>" onclick="icms.modal.openAjax('<?php echo href_to('forum', 'poll_vote_info', [$thread_poll['id'], $answer_id]); ?>', {}, false, '<?php html(sprintf(LANG_FORUM_POLL_PRESS_FOR_DETAIL, $questions)); ?>');">
                                        <?php echo html_spellcount($thread_poll['results']['answers'][$answer_id], LANG_FORUM_POLL_VOICE_SPELL); ?>
                                    </a>
                                <?php } else { ?>
                                    <?php echo html_spellcount($thread_poll['results']['answers'][$answer_id], LANG_FORUM_POLL_VOICE_SPELL); ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                    <div class="mt-3">
                        <span class="mr-3 small text-muted">
                            <?php echo $thread_poll['results']['total'] ? LANG_FORUM_TOTAL_VOTES : ''; ?> <?php echo html_spellcount($thread_poll['results']['total'], LANG_FORUM_POLL_VOICE_SPELL); ?>
                        </span>
                        <?php if($thread_poll['is_can_vote'] && !$thread_poll['is_closed'] && !$thread_poll['user_answer_ids']) { ?>
                            <a class="btn btn-outline-secondary btn-sm" onclick="return icms.forum.loadPoll('form');" href="#">
                                <?php echo LANG_FORUM_POLL_GOTO_VOTE; ?>
                            </a>
                        <?php } ?>
                        <?php if($thread_poll['is_closed']) { ?>
                            <span class="small text-muted">
                                <?php echo LANG_FORUM_POLL_FINISHED; ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>

            <?php } elseif($thread_poll['is_can_vote']) { ?>

                <?php if ($thread['is_closed'] && empty($thread_poll['user_answer_ids'])) { ?>
                    <div class="alert alert-info m-0">
                        <?php echo LANG_FORUM_YOU_IS_NOT_VOTE_IN_CLOSED; ?>
                    </div>
                <?php } elseif ($thread_poll['is_closed']) { ?>
                    <div class="alert alert-info m-0">
                        <?php echo LANG_FORUM_POLL_FINISHED; ?>
                    </div>
                <?php } elseif (!empty($thread_poll['user_answer_ids'])) { ?>
                    <div class="alert alert-info m-0">
                        <?php echo LANG_FORUM_YOU_IS_VOTE; ?>
                    </div>
                <?php } else { ?>

                    <form id="thread-poll-form" method="post" action="">
                        <?php echo html_input('hidden', 'csrf_token', cmsForm::getCSRFToken()); ?>
                        <?php echo html_input('hidden', 'poll_id', $thread_poll['id']); ?>

                        <div class="form-group">
                            <?php foreach ($thread_poll['answers'] as $answer_id => $questions) { ?>

                                <?php $aid = 'answer-'.$answer_id; ?>
                                <?php $type = !empty($thread_poll['options']['multi_answer']) ? 'checkbox' : 'radio'; ?>

                                <div class="custom-control custom-<?php echo $type; ?>">
                                    <input type="<?php echo $type; ?>" id="<?php echo $aid; ?>" value="<?php html($answer_id); ?>" name="answer" class="custom-control-input">
                                    <label class="custom-control-label" for="<?php echo $aid; ?>">
                                        <?php echo $questions; ?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>

                        <div>
                            <?php echo html_button(LANG_FORUM_VOTING, 'submit', 'icms.forum.pollSubmit(this);', ['class' => 'btn-primary']); ?>
                            <?php if ($thread_poll['allow_show_result']) { ?>
                                <a class="btn btn-light ml-2" onclick="return icms.forum.loadPoll('result');" href="#">
                                    <span>
                                        <?php echo LANG_FORUM_POLL_GOTO_RESULT; ?>
                                        <span class="badge badge-secondary"><?php echo $thread_poll['results']['total']; ?></span>
                                    </span>
                                </a>
                            <?php } ?>
                            <?php if ($thread_access->is_can_poll_delete) { ?>
                                <a class="btn btn-danger" href="#" onclick="return icms.forum.deletePoll();" title="<?php echo LANG_FORUM_DELETE_POLL; ?>" data-toggle="tooltip" data-placement="top">
                                    <?php html_svg_icon('solid', 'minus-circle'); ?>
                                </a>
                            <?php } ?>
                        </div>
                    </form>

                <?php } ?>

            <?php } else { ?>
                <div class="alert alert-info m-0">
                    <?php echo sprintf(LANG_FORUM_GUESTS_NOT_VOTE, href_to('auth', 'register')); ?>
                </div>
            <?php } ?>
        </div>
    </div>

</div>