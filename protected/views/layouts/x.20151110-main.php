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
	<link rel="shortcut icon" href="<?php echo Yii::app()->request->baseUrl; ?>/css/favicon.ico" />

	<title><?php echo CHtml::encode($this->pageTitle); ?></title>

	<script type="text/javascript">
		BaseUrl = "<?php echo Yii::app()->getBaseUrl(true); ?>/";
	</script>
	<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(true); ?>/assets/js/multiselect.min.js"></script>
	<script type="text/javascript" src="<?php echo Yii::app()->getBaseUrl(true); ?>/assets/js/vendor/MooTools-More-1.5.1-compressed.js"></script>
	<?php echo $this->extraJS; ?>

</head>

<body>

<div class="container" id="page">

	<div id="header">
		<div id="logo"><?php echo CHtml::encode(Yii::app()->name); ?></div>
	</div><!-- header -->

	<div id="mainmenu">
		<?php $this->widget('zii.widgets.CMenu',array(
			'items'=>array(
				//array('label'=>'User Management', 'url'=>array('/users'), 'visible'=>!Yii::app()->user->isGuest && Yii::app()->user->AccessType=="SUPERADMIN"),
				array('label'=>'User Management', 'url'=>array('/mgmtUsers'), 'visible'=>!Yii::app()->user->isGuest && Yii::app()->user->AccessType=="SUPERADMIN"),
				array('label'=>'Customers ', 'url'=>array('/customers'), 'visible'=>!Yii::app()->user->isGuest && Yii::app()->user->AccessType=="SUPERADMIN"),
				array('label'=>'Clients', 'url'=>array('/clients'), 'visible'=>!Yii::app()->user->isGuest),
				
				
				array('label'=>'Brands', 'url'=>array('/brands'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Campaigns', 'url'=>array('/campaigns'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Channels', 'url'=>array('/channels'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Points System', 'url'=>array('/pointsSystem'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Points Mapping','url'=>array('/pointsSystemMapping'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Points Action Type', 'url'=>array('/actionType'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Rewards List', 'url'=>array('/rewardsList'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Rewards and Redemption', 'url'=>array('/rewardDetails'), 'visible'=>!Yii::app()->user->isGuest),

				array('label'=>'Coupon System', 'url'=>array('/couponSystem'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Coupon to Points', 'url'=>array('/couponToPoints'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Raffle', 'url'=>array('/raffle'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Scheduled Event Post', 'url'=>array('/schedEventPost'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Customer Report', 'url'=>array('/reportsList'), 'visible'=>!Yii::app()->user->isGuest),
				
				//array('label'=>'Scheduled Post', 'url'=>array('/scheduledPost'), 'visible'=>!Yii::app()->user->isGuest),
				//array('label'=>'Convert Points to Coupon', 'url'=>array('/pointsToCoupon'), 'visible'=>!Yii::app()->user->isGuest),
				array('label'=>'Query Report', 'url'=>array('/tableQuery'), 'visible'=>!Yii::app()->user->isGuest && Yii::app()->user->AccessType=="SUPERADMIN"),
				array('label'=>'Audit Logs', 'url'=>array('/auditLogs'), 'visible'=>!Yii::app()->user->isGuest && Yii::app()->user->AccessType=="SUPERADMIN"),
				
				array('label'=>'Login', 'url'=>array('/login'), 'visible'=>Yii::app()->user->isGuest),
				array('label'=>'Logout ('.Yii::app()->user->name.')', 'url'=>array('/logout'), 'visible'=>!Yii::app()->user->isGuest)
			),
		)); ?>
	</div><!-- mainmenu -->
	<?php if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		));
		?><!-- breadcrumbs -->
	<?php endif?>

	<?php echo $content; ?>

	<div class="clear"></div>

	<div id="footer">
		Copyright &copy; <?php echo date('Y'); ?> by MRM.<br/>
		All Rights Reserved.<br/>
	</div><!-- footer -->

</div><!-- page -->
<?php
//Audit-Logs
if (!Yii::app()->user->isGuest)
{
	if(0){
	$utilLog = new Utils;
	$utilLog->saveAuditLogs();
	}
}
?>
</body>
</html>
