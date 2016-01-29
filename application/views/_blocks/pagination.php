<!--Блок пагинации для таблиц и списков-->


<!--Проверяем пришли ли все необходимые параметры-->
<?php if(isset($url) && isset($page) && $page && isset($pages) && $pages && $pages > 1):?>
<nav>
    <ul class="pagination">
        <li <?= $page == 1 ? 'class="disabled"' : ''?>>
            <a href="<?=base_url().$url.'/'.($page - 1)?>" aria-label="<?=lang('previous')?>">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <?php for($i = 1; $i <= $pages; $i++):?>
        <li <?= $page == $i ? 'class="active"' : ''?>><a href="<?=base_url().$url.'/'.$i?>"><?=$i?></a></li>
        <?php endfor;?>
        <li <?= $page == $pages ? 'class="disabled"' : ''?>>
            <a href="<?=base_url().$url.'/'.($page + 1)?>" aria-label="<?=lang('next')?>">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
    </ul>
</nav>
<?php endif;?>

<!-- /Блок пагинации для таблиц и списков-->
