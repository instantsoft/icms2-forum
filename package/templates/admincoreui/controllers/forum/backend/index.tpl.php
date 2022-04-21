<?php

$this->addTplJSName([
    'jquery-cookie',
    'datatree',
    'admin-content'
]);

$this->addTplCSSName('datatree');

$this->setPageTitle(LANG_FORUM_FORUMS);
$this->addBreadcrumb(LANG_FORUM_FORUMS, $this->href_to('forum'));

$this->addToolButton(array(
    'class' => 'menu d-xl-none',
    'data'  => [
        'toggle' =>'quickview',
        'toggle-element' => '#left-quickview'
    ],
    'title' => LANG_MENU
));

$this->addToolButton(array(
    'class' => 'add_folder',
    'title' => LANG_CP_FORUM_CAT_CREATE,
    'href'  => $this->href_to('category_add')
));

$this->addToolButton(array(
    'class' => 'edit_folder',
    'title' => LANG_FORUM_CAT_EDIT,
    'href'  => $this->href_to('category_edit')
));

$this->addToolButton(array(
    'class'   => 'delete_folder',
    'title'   => LANG_CP_FORUM_CAT_DELETE,
    'href'    => $this->href_to('category_delete'),
    'confirm' => LANG_CP_FORUM_CAT_DELETE_CONFIRM
));

$this->addToolButton(array(
    'class'   => 'tree_folder',
    'title'   => LANG_CP_FORUM_CAT_ORDER,
    'href'    => $this->href_to('category_order'),
    'onclick' => 'return categoryReorder($(this))'
));

$this->applyToolbarHook('admin_forum_toolbar');

?>

<div class="row align-items-stretch mb-4">
    <div class="col-sm col-xl-3 quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData">
                <li id="1" class="lazy folder"><?php echo LANG_CP_FORUM_CAT_ROOT; ?></li>
            </ul>
        </div>
    </div>

    <div class="col-sm-12 col-xl-9">
        <?php $this->renderGrid(false, $grid); ?>
    </div>
</div>

<?php ob_start(); ?>
<script>
    $( function () {

        $( "#datatree" ).dynatree( {
            onPostInit: function ( isReloading, isError ) {
                var path = $.cookie('icms[forum_tree_path]');
                if (!path) {
                    path = '1';
                }
                if (path) {
                    $("#datatree").dynatree('getTree').loadKeyPath( path, function (node, status) {
                        if (status === 'loaded') {
                            node.expand();
                        } else if (status === 'ok') {
                            node.activate();
                            node.expand();
                            icms.datagrid.init();
                        }
                    });
                }
            },
            onActivate: function ( node ) {
                node.expand();
                $.cookie( 'icms[forum_tree_path]', node.getKeyPath(), {
                    expires: 7, path: '/'
                } );
                var key = node.data.key;
                icms.datagrid.setURL( "<?php echo $this->href_to('category_ajax'); ?>/" + key );
                $( '.cp_toolbar .add_folder a' ).attr( 'href', "<?php echo $this->href_to('category_add'); ?>/" + key );
                $( '.cp_toolbar .edit_folder a' ).attr( 'href', "<?php echo $this->href_to('category_edit'); ?>/" + key );
                $( '.cp_toolbar .delete_folder a' ).attr( 'href', "<?php echo $this->href_to('category_delete'); ?>/" + key + '?csrf_token=' + icms.forms.getCsrfToken() );

                if ( key === '1' ) {
                    $( '.cp_toolbar .edit_folder a' ).hide();
                    $( '.cp_toolbar .delete_folder a' ).hide();
                    $( '.cp_toolbar .add a' ).hide();
                } else {
                    $( '.cp_toolbar .edit_folder a' ).show();
                    $( '.cp_toolbar .delete_folder a' ).show();
                    $( '.cp_toolbar .add a' ).show();
                }
                $('.breadcrumb-item.active').html(node.data.title);
                icms.datagrid.loadRows();
            },
            onLazyRead: function ( node ) {
                node.appendAjax({
                    url: '<?php echo href_to('forum', 'category_tree_ajax'); ?>',
                    data: {
                        id: node.data.key
                    }
                });
            }
        } );
    } );

    function categoryReorder( button ) {
        icms.modal.openAjax(button.attr('href'),{},false,$(button).attr('title'));
        return false;
    }
</script>
<?php $this->addBottom(ob_get_clean()); ?>