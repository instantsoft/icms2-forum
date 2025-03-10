<?php
$this->addTplCSSName('datatree');
$this->addTplJSName('datatree');

$this->addBreadcrumb(LANG_FORUM_FORUMS, $this->href_to('forum'));

$this->applyToolbarHook('admin_forum_toolbar');
?>

<div class="row flex-nowrap align-items-stretch mb-4">
    <div class="col-sm col-xl-3 col-xxl-2 quickview-wrapper" id="left-quickview">
        <a class="quickview-toggle close" data-toggle="quickview" data-toggle-element="#left-quickview" href="#"><span aria-hidden="true">×</span></a>
        <div id="datatree" class="card-body bg-white h-100 pt-3">
            <ul id="treeData" class="skeleton-tree">
                <li id="1" class="lazy folder">
<?php echo LANG_CP_FORUM_CAT_ROOT; ?>
                </li>
            </ul>
        </div>
    </div>
    <div class="col-sm-12 col-xl-9 col-xxl-10">
<?php echo $grid_html; ?>
    </div>
</div>

<script>
var icms = icms || {};

icms.events.on('datagrid_mounted', function(gridApp){

    let cp_toolbar = $('.cp_toolbar');
    let is_init = false;

    $('#datatree').dynatree({
        debugLevel: 0,
        onPostInit: function(isReloading, isError){
            let path = '<?php html($key_path); ?>';
            this.loadKeyPath(path, function(node, status){
                if(status === 'loaded') {
                    node.expand();
                } else if(status === 'ok') {
                    node.activate();
                    node.expand();
                }
            });
        },
        onActivate: function(node){
            node.expand();
            $.cookie('icms[forum_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
            let key = node.data.key;
            icms.datagrid.setURL('<?= $this->href_to('index'); ?>/' + key);
            if (is_init) {
                icms.datagrid.loadRows();
            }
            is_init = true;

            gridApp.select_actions_items_map = key;

            $('.add_folder a', cp_toolbar).attr('href', '<?= $this->href_to('category_add'); ?>/' + key);
            $('.edit_folder a', cp_toolbar).attr('href', '<?= $this->href_to('category_edit'); ?>/' + key);
            $('.delete_folder a', cp_toolbar).attr('href', '<?= $this->href_to('category_delete'); ?>/' + key + '?csrf_token=' + icms.forms.getCsrfToken());

            if (key === '1'){
                $('.edit_folder a', cp_toolbar).hide();
                $('.delete_folder a', cp_toolbar).hide();
            } else {
                $('.folder', cp_toolbar).addClass('animated animate-shake');
                $('.edit_folder a', cp_toolbar).show();
                $('.delete_folder a', cp_toolbar).show();
            }
            var root_node = null;
            node.visitParents(function (_node) {
                if(_node.parent !== null){
                    root_node = _node;
                }
            }, true);
            window.history.pushState(null, null, '<?= $this->href_to('index'); ?>/'+key);
        },
        onLazyRead: function(node){
            node.appendAjax({
                url: '<?php echo href_to('forum', 'category_tree_ajax'); ?>',
                data: {
                    id: node.data.key
                }
            });
        }
    });
});

function categoryReorder(button) {
    event.preventDefault();
    icms.modal.openAjax(button.attr('href'), {}, false, $(button).attr('title'));
    return false;
}

</script>
