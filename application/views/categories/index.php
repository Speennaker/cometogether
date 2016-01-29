

<!--<h2 class="sub-header">--><?//=lang('poses_categories_list');?><!--</h2>-->
<div class="table-responsive">
    <?php $this->load->view('_blocks/bulk_actions', ['actions' => $this->bulk_actions], false);?>
    <table class="table table-striped">
        <thead>
        <tr>
            <?php if(isset($this->bulk_actions) && $this->bulk_actions):?><th><input type="checkbox" id="mark_all_checkbox" value="1"></th><?php endif;?>
            <th><?=lang('category_title')?></th>
            <th><?=lang('category_subcategories')?></th>
            <th><?=lang('category_poses')?></th>
            <th><?=lang('category_active')?></th>
            <th><?=lang('category_published')?></th>
            <th><?=lang('category_free')?></th>
            <th width="200"></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($list as $row):?>
            <tr>
                <?php if(isset($this->bulk_actions) && $this->bulk_actions):?><td><input type="checkbox" class="table_checkbox" data-id="<?=$row['id']?>" value="1"></td><?php endif;?>
                <td><?=$row['title']?></td>
                <td><?=count($row['subcategories']) ?: '-'?></td>
                <td><?=$row['poses']?></td>
                <td><input type="checkbox" <?=$row['active'] ? "checked" : ""?> disabled value="1"></td>
                <td><input type="checkbox" <?=$row['published'] ? "checked" : ""?> disabled value="1"></td>
                <td><input type="checkbox" <?=$row['free'] ? "checked" : ""?> disabled value="1"></td>
                <td>
                    <div class="btn-group" role="group" aria-label="...">
                        <a type="button" class="btn btn-primary" data-toggle="tooltip" data-placement="top" title="<?=lang('add_subcategory')?>">
                            <span class="glyphicon glyphicon-list" aria-hidden="true"></span>
                        </a>
                        <a type="button" class="btn btn-success" data-toggle="tooltip" data-placement="top" title="<?=lang('add_poses')?>">
                            <span class="glyphicon glyphicon-camera" aria-hidden="true"></span>
                        </a>
                        <a href="<?=base_url().$this->module.'/edit/'.$row['id']?>" type="button" class="btn btn-warning" data-toggle="tooltip" data-placement="top" title="<?=lang('edit')?>">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        </a>
                        <a type="button" class="btn btn-danger" data-toggle="tooltip" data-placement="top" title="<?=lang('delete')?>">
                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                        </a>
                    </div>
                </td>
            </tr>
        <?php endforeach;?>
        </tbody>
    </table>
    <?php $this->load->view('_blocks/pagination', ['url' => 'categories', 'page' => $page, 'pages' => $pages], false);?>
</div>
