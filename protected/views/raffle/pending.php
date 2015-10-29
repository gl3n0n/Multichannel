<?php
/* @var $this RaffleController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Raffles',
);

$this->menu=array(
	array('label'=>'Create Raffle', 'url'=>array('create')),
	// array('label'=>'Manage Raffle', 'url'=>array('admin')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN" or Yii::app()->user->AccessType === "ADMIN")
{
	$this->menu=array(
	array('label'=>'Create Raffle',   'url'=>array('create')),
	array('label'=>'Pending Raffles', 'url'=>array('pending')),
	);
}
?>

<h1>Pending Raffles</h1>
<div>

<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("raffle/pending"),
	'method'=>'get',
)); 
include_once(Yii::app()->basePath . '/views/filters/filter-byclients-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-byname-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bypointsystem-name-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-from-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-daterange-to-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-bystatus-form.php');
include_once(Yii::app()->basePath . '/views/filters/filter-submit-btn-form.php');
 $this->endWidget(); 
?>
</div>
<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
		'name' => 'RaffleId',
		'type' => 'raw',
		'value'=> 'CHtml::link($data->RaffleId,Yii::app()->createUrl("raffle/view",array("id"=>$data->primaryKey)))',
		), 
		'RaffleName',
		array(
		'name'  => 'Coupon Name',
		'value' => '($data->raffleCoupon!=null)?($data->raffleCoupon->CouponName):("")',
		),
		array(
			'name' => 'Points System Name',
			'value'=> '($data->byPoints!=null and @count($data->byPoints))?$data->byPoints[0]->Name:""',
			),	
		array(
		'name' => 'ClientId',
		'value'=> '$data->raffleClients!=null?$data->raffleClients->CompanyName:""',
		),		
		'NoOfWinners',
		'BackUp',
		'FdaNo',
		'Source',
		'DrawDate',
		'Status',
		array(
		'name' => 'Action',
		'type' => 'raw',
		'value'=> '($data->Status !== "PENDING")?(CHtml::link("Generate Participants",Yii::app()->createUrl("raffle/genraffle",array("raffleid"=>$data->primaryKey,"couponid"=>"$data->CouponId","numwinners"=>"$data->NoOfWinners")))):(CHtml::link("Approve",Yii::app()->createUrl("raffle/approve/",array("uid"=>$data->primaryKey))))'
		), 
		),		
)); ?>
