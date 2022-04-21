var icms = icms || { };
var LANG_FORUM_ATTACH_FILES = LANG_FORUM_ATTACH_FILES || '';

icms.forum = (function ( $ ) {

    this.onDocumentReady = function () {

        var add_files = $( '#f_files' );

        if ( add_files !== undefined ) {

            add_files.before( '<div class="field add_file"><label>' + LANG_FORUM_ATTACH_FILES + '</label></div>' );

            $( '.add_file' ).on( 'click', function () {
                add_files.toggle( 'slow' );
            } );

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

    this.deleteThread = function ( url ) {

        $.ajax( {
            type: 'POST',
            url: url + '?csrf_token=' + icms.forms.getCsrfToken(),
            dataType: 'json'
        } ).done( function ( result ) {

            if ( !result['error'] ) {
                window.location.href = result['redirect'];
            }

        } );

    };

    this.deleteFile = function ( url, post_id ) {

        $.post( url + '?csrf_token=' + icms.forms.getCsrfToken(), function ( result ) {

            if ( result ) {
                $( '#fa_attach_' + post_id ).css( 'background', '#ffaeae' ).fadeOut();
            }

            return false;

        } );

    };

    this.viewPost = function ( url, post_id ) {

        $.ajax( {
            url: url,
            dataType: 'json'
        } ).done( function ( data ) {
            $( '#post_content_' + post_id ).html( data );
        } );

    };

    this.postFastEdit = function ( url, post_id ) {

        var content = $( '#post_content_' + post_id );

        if ( content.parent().children( '.fast-content-edit-form' ).length < 1 ) {

            $.ajax( {
                url: url
            } ).done( function ( data ) {
                content.hide();
                content.after( data );
            } );

        }

        return false;

    };

    this.cancelPostFastEdit = function ( post_id ) {

        var content = $( '#post_content_' + post_id );

        content.next( 'link' ).remove();
        content.parent().children( '.fast-content-edit-form' ).remove();
        content.show();

        return false;

    };

    this.deletePost = function ( url ) {

        $.post( url + '?csrf_token=' + icms.forms.getCsrfToken(), function ( result ) {
            if ( result.error === false ) {
                window.location.href = result.redirect;
            }
        }, 'json' );

    };

    this.previewPost = function ( url ) {

        icms.events.run( 'forumBeforePreviewPost', {
            id: 'textarea#content'
        } );

        var content = $( 'textarea#content' ).val();

        var data = {
            content: content, is_flood: $( 'select#is_flood' ).val()
        };

        icms.modal.openAjax( url, data, setTimeout( function () {
            icms.modal.resize();
        }, 750 ) );

        icms.modal.resize();

        return false;

    };

    this.addQuoteText = function ( obj ) {

        var seltext = '', quote = '';

        var quote_link = $( obj );
        var author = quote_link.attr( 'data-author' );
        var url = quote_link.attr( 'data-url' );

        if ( window.getSelection || document.getSelection() ) {

            var selections = window.getSelection() || document.getSelection();

            var frag = selections.getRangeAt( 0 ).cloneContents();

            var span = document.createElement('span');
            span.appendChild(frag);

            seltext = span.innerHTML;

        } else if ( document.selection ) {

            seltext = document.selection.createRange().text;

        }

        if ( seltext ) {

            quote = '<blockquote><strong>' + author + ':</strong><br>' + seltext + ' <a class="link-blockquote" href="' + url + '"></a></blockquote><br>' + "\n";

            icms.forms.wysiwygAddText( 'content', quote );

            icms.events.run( 'forumAddQuote', {
                id: 'textarea#content', content: seltext, author: author, url: url
            } );

        } else {

            alert( LANG_FORUM_SELECT_TEXT_QUOTE );

        }

    };

    this.addNickname = function (obj) {

        var nickname = ' <b>' + $.trim($(obj).text()) + '</b>, ';

        icms.forms.wysiwygAddText('content', nickname);

        icms.events.run('forumAddNickname', {
            id: 'textarea#content', content: nickname
        });

        return false;
    };

    return this;

}).call( icms.forum || { }, jQuery );