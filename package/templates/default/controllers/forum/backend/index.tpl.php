<?php
$this->addTplCSSName('datatree');
$this->addTplJSName([
    'datatree',
    'admin-content'
]);

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

<?php ob_start(); ?>
<script>

    $(function () {

        let cp_toolbar = $('.cp_toolbar');
        let is_init = false;

        $("#datatree").dynatree({
            debugLevel: 0,
            onPostInit: function (isReloading, isError) {
                let path = '<?php echo $key_path; ?>';
                this.loadKeyPath(path, function (node, status) {
                    if (status === "loaded") {
                        node.expand();
                    } else if (status === "ok") {
                        node.activate();
                        node.expand();
                    }
                });
            },
            onActivate: function (node) {

                node.expand();
                $.cookie('icms[forum_tree_path]', node.getKeyPath(), {expires: 7, path: '/'});
                let key = node.data.key;

                $('.add_folder a', cp_toolbar).attr('href', '<?php echo $this->href_to('category_add'); ?>/' + key);
                $('.edit_folder a', cp_toolbar).attr('href', '<?php echo $this->href_to('category_edit'); ?>/' + key);
                $('.delete_folder a', cp_toolbar).attr('href', '<?php echo $this->href_to('category_delete'); ?>/' + key + '?csrf_token=' + icms.forms.getCsrfToken());

                if (is_init) {
                    icms.datagrid.loadRows();
                }
                is_init = true;

                if (key === '1') {
                    $('.cp_toolbar .edit_folder a').hide();
                    $('.cp_toolbar .delete_folder a').hide();
                    $('.cp_toolbar .add a').hide();
                } else {
                    $('.cp_toolbar .edit_folder a').show();
                    $('.cp_toolbar .delete_folder a').show();
                    $('.cp_toolbar .add a').show();
                }

                $('.breadcrumb-item.active').html(node.data.title);

            },
            onLazyRead: function (node) {
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
        icms.modal.openAjax(button.attr('href'), {}, false, $(button).attr('title'));
        return false;
    }
</script>
<?php $this->addBottom(ob_get_clean()); ?>
