<!--Блок действий для нескольких элементов-->

<!--Проверяем пришли ли все необходимые параметры-->
<?php if(isset($actions) && is_array($actions) && $actions):?>
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle disabled" id="bulk_actions" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?=lang('choose_action')?>... <span class="caret"></span>
            </button>
            <?php $separated = array_key_exists('separated', $actions) ? $actions['separated'] : []; unset($actions['separated']);?>
            <ul class="dropdown-menu" id="bulk_actions_list">
                <?php foreach($actions as $action):?>
                    <li class="bg-<?=$action['class']?>"><a href="javascript: void(0);" data-url="<?=base_url().$action['url']?>"><p class="text-<?=$action['class']?>"><?= $action['title']?></p></a></li>
                <?php endforeach;?>
                <?php if($separated):?>
                    <li role="separator" class="divider"></li>
                    <?php foreach($separated as $s_action):?>
                        <?php if(strpos($s_action['url'], 'delete') != false): ?>
                            <li class="bg-<?=$s_action['class']?>"><a href="javascript: void(0);" data-url="<?=base_url().$s_action['url']?>"><p class="text-<?=$s_action['class']?>"><?= $s_action['title']?></p> </a></li>
                        <?php endif;?>


                    <?php endforeach;?>
                <?php endif;?>
            </ul>
        </div>
    <div class="modal fade bs-example-modal-sm" id="confirmationModal" role="dialog" aria-labelledby="mySmallModalLabel">
        <div class="modal-dialog modal-sm">
            <div class="modal-content ">
                <div class="modal-content">
                    <div class="modal-header bg-danger">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title text-danger"><?= lang('warning')?>!</h4>
                    </div>
                    <div class="modal-body">
                        <b class="text-danger"><?=lang('delete_confirmation')?></b>
                    </div>
                    <div class="modal-footer bg-danger">
                        <button type="button" class="btn btn-default" data-dismiss="modal"><?=lang('cancel')?></button>
                        <button type="button" id="deleteConfirmed" data-url="" class="btn btn-danger"><?=lang('delete')?></button>
                    </div>
                </div><!-- /.modal-content -->
            </div>
        </div>
    </div>

<?php endif;?>

<!-- /Блок действий для нескольких элементов-->