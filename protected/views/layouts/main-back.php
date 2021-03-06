<?php /* @var $this Controller */ ?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<meta name="language" content="en">

	<!-- blueprint CSS framework -->
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/screen.css" media="screen, projection">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/print.css" media="print">
	<!--[if lt IE 8]>
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ie.css" media="screen, projection">
	<![endif]-->

	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main.css">
	<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/form.css">

	<?php if(isset($this->extraCSS)) echo $this->extraCSS; ?>

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<script type="text/javascript">
		BaseUrl = "<?php echo Yii::app()->getBaseUrl(true); ?>/";
	</script>
	<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(true); ?>/assets/js/vendor/MooTools-More-1.5.1-compressed.js"></script>
	<?php if(isset($this->extraJS)) echo $this->extraJS; ?>

</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				array('label'=>'User Management', 'url'=>array('/users'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Clients', 'url'=>array('/clients'), 'visible'=>!Yii::app()->user->isGuest && !Yii::app()->user->isSuperAdmin()),
				array('label'=>'Brands', 'url'=>array('/brands'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Campaigns', 'url'=>array('/campaigns'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Channels', 'url'=>array('/channels'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Points System', 'url'=>array('/points'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Rewards and Redemption', 'url'=>array('/rewardDetails'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Rewards List', 'url'=>array('/rewardsList'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Login', 'url'=>array('/site/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/site/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by MRM.<br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->

</body>
</html>
