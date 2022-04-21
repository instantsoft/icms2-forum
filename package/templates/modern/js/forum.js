var icms = icms || { };

icms.forum = (function ($) {

    var self = this;

    this.enable_quote = false;

    this.onDocumentReady = function () {

        var poll_answers_input = $('[id ^= "f_poll_answers_"]');
        var poll_answers_count = poll_answers_input.length;

        $(poll_answers_input).each(function (indx, element){
            var input = $(element).find('input');
            $(input).on('input', function (){
                var value_length = $(this).val().length;
                if(indx === 0 || value_length > 0){
                    $(poll_answers_input).eq(indx+1).show();
                }
                if(value_length === 0){
                    for (var i = 1; i <= poll_answers_count; i++) {
                        $(poll_answers_input).eq(indx+i).hide().find('input').val('');
                    }
                }
            }).trigger('input');
        });

        if(this.enable_quote){
            $('.icms-forum__post-content').on('mouseup', function(e) {

                var text = self.getSelection();

                if (text) {
                    self.showQuoteText($(this).closest('.icms-forum__post'), e);
                }
            });
        }

        this.highlightPost();
    };

    this.highlightPost = function () {

        var anchor = window.location.hash;
        if (!anchor) {return false;}

        var find_id = anchor.match(/post-([0-9]+)/);
        if (!find_id) {return false;}

        $('.icms-forum__post-data').removeClass('selected-post shadow');
        var p = $('#post-'+find_id[1]);
        $('.icms-forum__post-data', p).addClass('selected-post shadow');
        return false;
    };

    this.viewPost = function (url, post_id) {

        $.ajax({
            url: url
        }).done( function ( data ) {
            $('#post-' + post_id +' .icms-forum__post-content').html(data);
        });

        return false;
    };

    this.getSelection = function() {

        var selections = window.getSelection();

        return selections.toString().length > 0 ? selections.getRangeAt(0).cloneContents() : false;
    };

    this.showQuoteText = function (post, e) {

        $('#post-toolbar').remove();

        var toolbar = $('<div id="post-toolbar" class="icms-forum__post-toolbar"><a href="#" onclick="return icms.forum.addQuoteText(\''+post.data('author')+'\');">'+LANG_FORUM_ADD_QUOTE_TEXT+'</div>');

        $('.icms-forum__post-data', post).append(toolbar);

        const rects = window.getSelection().getRangeAt(0).getClientRects();
        const firstRect = rects[0];

        const parentOffset = $(toolbar).offsetParent().offset();

        var left, top;

        var width = $(toolbar).width();

        left = e.offsetX;
        if ($('.icms-forum__post-content', post).width() < (left + width)) { left -= width; }

        top = $(window).scrollTop() + firstRect.top - parentOffset.top - $(toolbar).outerHeight() - 5;

        $(toolbar).css({
            left: left + 'px',
            top: top + 'px'
        });

        $(document).one('mousedown', function(e) {
            if ($(e.target).closest(toolbar).length === 0) {
                $(toolbar).remove();
            }
        });
    };

    this.addQuoteText = function (author) {

        var sel = this.getSelection();
        var seltext = false;

        if(sel){
            var span = document.createElement('span');
            span.appendChild(this.getSelection());

            seltext = span.innerHTML;
        }

        if (seltext) {

            var quote = '<blockquote><p>' + seltext + '</p><footer class="blockquote-footer"><cite>' + author + '</cite></footer></blockquote><br>';

            icms.forms.wysiwygAddText('content', quote);

            icms.events.run( 'forumAddQuote', {
                id: 'textarea#content', content: seltext, author: author
            });

        } else {
            alert( LANG_FORUM_SELECT_TEXT_QUOTE );
        }

        $('#post-toolbar').remove();

        return false;
    };

    this.addNickname = function (obj) {

        var nickname = ' <b>' + $.trim($(obj).text()) + '</b>, ';

        icms.forms.wysiwygAddText('content', nickname);

        icms.events.run('forumAddNickname', {
            id: 'textarea#content', content: nickname
        });

        return false;
    };

    this.pollSubmit = function (btn) {

        var thread_poll = $('#icms-thread-poll');

        $(btn).addClass('is-busy');

        var form = $(btn).closest('form');

        var form_data = icms.forms.toJSON(form);

        $.post(thread_poll.data('vote_url'), form_data, function (result) {

            $(btn).removeClass('is-busy');

            if (result.error === false) {

                self.loadPoll('result');

                return;
            } else {
                alert(result.text);
            }

        }, 'json');

        return false;
    };

    this.loadPoll = function(action) {

        var thread_poll = $('#icms-thread-poll');

        $(thread_poll).css({
            opacity: 0.4
        });
        $.post(thread_poll.data('poll_url')+'/' + action, {}, function (data) {
            $(thread_poll).html(data);
            $(thread_poll).css({
                opacity: 1.0
            });
        });

        return false;
    };

    this.deletePoll = function () {

        var thread_poll = $('#icms-thread-poll');

        if (confirm(thread_poll.data('confirm_delete'))) {
            $.post(thread_poll.data('poll_delete_url'), {csrf_token: icms.forms.getCsrfToken()}, function (result) {
                $(thread_poll).html('');
                alert(result.text);
            }, 'json');
        }

        return false;
    };

    this.loadPollInfo = function(link){

        var pagination = $('#icms-forum-poll-info-pagination');

        var url = $(pagination).data('url');

        var page = $(link).data('page');
        var list = $('#icms-forum-poll-info-list');

        $('a', pagination).removeClass('active');
        $(link).addClass('active is-busy');

        $.post(url, {
            page: page,
            is_list_only: true
        }, function(result){

            list.html(result);
            setTimeout(function (){
                $(link).removeClass('is-busy');
            }, 200);

        }, "html");

        return false;
    };

    return this;

}).call( icms.forum || { }, jQuery );