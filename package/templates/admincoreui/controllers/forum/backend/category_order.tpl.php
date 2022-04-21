<?php if (empty($categories)){ ?>
    <div class="alert alert-warning m-0" id="alert_wrap">
        <?php echo LANG_CP_FORUM_CATS_NONE; ?>
    </div>
<?php } else { ?>
    <form action="<?php echo $this->href_to('category_order'); ?>" onsubmit="return saveCategoryOrder( $( this ) )" method="post">
        <fieldset class="modal_treeview">
            <legend><?php echo LANG_CP_FORUM_CATS_ORDER_DRAG; ?></legend>
            <div id="ordertree">

                <ul id="treeData" >

                    <?php $last_level = 0; ?>

                    <?php foreach ($categories as $id => $item) { ?>

                        <?php
                        if (!isset($item['ns_level'])) {
                            $item['ns_level'] = 1;
                        }
                        $item['childs_count'] = ($item['ns_right'] - $item['ns_left']) > 1;
                        ?>

                        <?php for ($i = 0; $i < ($last_level - $item['ns_level']); $i++) { ?>
                            </li></ul>
                        <?php } ?>

                    <?php if ($item['ns_level'] <= $last_level) { ?>
                        </li>
                    <?php } ?>

                    <li class="folder" id="<?php echo $id; ?>">
        <?php html($item['title']); ?>

                        <?php if ($item['childs_count']) { ?><ul><?php } ?>

                        <?php $last_level = $item['ns_level']; ?>

                        <?php } ?>

                        <?php for ($i = 0; $i < $last_level; $i++) { ?>
                    </li></ul>
                        <?php } ?>
                </ul>
            </div>
        </fieldset>
        <?php echo html_input('hidden', 'hash', ''); ?>
        <?php echo html_submit(LANG_SAVE); ?>
    </form>
<script>
    $( "#ordertree" ).dynatree( {
        dnd: {
            onDragStart: function ( node ) {
                return true;
            },
            autoExpandMS: 1000,
            preventVoidMoves: true,
            onDragEnter: function ( node, sourceNode ) {
                return true;
            },
            onDragOver: function ( node, sourceNode, hitMode ) {
                if ( node.isDescendantOf( sourceNode ) ) {
                    return false;
                }
                if ( !node.data.isFolder && hitMode === "over" ) {
                    return "after";
                }
            },
            onDrop: function ( node, sourceNode, hitMode, ui, draggable ) {
                sourceNode.move( node, hitMode );
                node.expand( true );
            },
            onDragLeave: function ( node, sourceNode ) {}
        }
    } );

    function saveCategoryOrder( form ) {
        var dict = $( '#ordertree' ).dynatree( 'getTree' ).toDict();
        $( 'input:hidden', form ).val( JSON.stringify( dict ) );
        return true;
    }

</script>
<?php } ?>