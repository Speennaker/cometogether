<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="width=device-width, initial-scale=1" name="viewport">
 	<title><?=lang($menu_item.'_page_title')?></title>
	<?= js('../../vendor/components/jquery/jquery.js')?>
	<?= js('../../vendor/twbs/bootstrap/dist/js/bootstrap.min.js')?>
	<?= js('uploadify/jquery.uploadify.min.js')?>
    <script type="text/javascript"> var base_url = '<?=base_url()?>';</script>
    <?= js('main.js')?>
	<?php foreach($custom_js as $r):?>
		<?= js($r)?>
	<?php endforeach;?>
	<?= css('../../vendor/twbs/bootstrap/dist/css/bootstrap.min.css')?>
	<?= css('../js/uploadify/uploadify.css')?>
	<?= css('main.css')?>
	<?php foreach($custom_css as $c):?>
		<?= css($c)?>
	<?php endforeach;?>

</head>
<body>
	<nav class="navbar navbar-inverse navbar-fixed-top">
		<div class="container-fluid">
			<div class="navbar-header">
					<a class="navbar-brand" href="<?= base_url()?>"><?=lang('project_name')?></a>
			</div>
			<div id="navbar" class="collapse navbar-collapse">
				<form class="navbar-form navbar-right">
					<div class="form-group">
						<button class="btn btn-danger" type="submit">Sign out</button>
					</div>
				</form>
			</div>
		</div>
	</nav>
	<div class="container-fluid">
		<div class="row">
<!--			Боковое Меню-->
            <?php if($menus):?>
            <div class="col-sm-3 col-md-2 sidebar">
				<?php foreach($menus as $menu):?>
                    <ul class="nav nav-sidebar">
                        <?php foreach($menu as $key => $item):?>
                        <li <?= $key == $menu_item ? 'class="active" ' : ''?>><a href="<?=base_url().$item['url']?> "><?=$item['title']?>
<!--                            Метка для пропуска навигации ???-->
                                <?php if($key == $menu_item):?>    <span class="sr-only">(current)</span><?php endif;?>
<!--                            /Метка для пропуска навигации ???-->
                            </a></li>
                        <?php endforeach;?>
                    </ul>
                <?php endforeach;?>
			</div>

            <?php endif;?>
<!--			/Боковое Меню-->
			<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
               <?php if($breadcrumbs):?>
                <ol class="breadcrumb">
                    <?php $current = array_pop($breadcrumbs);?>
                    <?php foreach($breadcrumbs as $bc):?>
                        <li><a href="<?=base_url('/').$bc['url']?>"><?=$bc['title']?></a></li>
                    <?php endforeach;?>
                    <li class="active"><?=$current['title']?></li>
                </ol>
                <?php endif;?>
                <h1 class="page-header"><?=lang($menu_item.'_page_title')?></h1>




