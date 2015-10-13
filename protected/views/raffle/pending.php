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
)); ?>
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	</fieldset>
<?php $this->endWidget(); ?>
</div>
<?php 

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',CHtml::link(CHtml::encode($data->RaffleId), array('view', 'id'=>$data->RaffleId))
	'columns'=>array(
		'CouponId',
		array(
		'name' => 'RaffleId',
		'type' => 'raw',
		'value'=> 'CHtml::link($data->RaffleId,Yii::app()->createUrl("raffle/view",array("id"=>$data->primaryKey)))',
		), 
		'NoOfWinners',
		'FdaNo',
		'Source',
		array(
		'name' => 'Action',
		'type' => 'raw',
		'value'=> '($data->Status !== "PENDING")?(CHtml::link("Generate Participants",Yii::app()->createUrl("raffle/genraffle",array("raffleid"=>$data->primaryKey,"couponid"=>"$data->CouponId","numwinners"=>"$data->NoOfWinners")))):(CHtml::link("Approve",Yii::app()->createUrl("raffle/approve/",array("uid"=>$data->primaryKey))))'
		), 
	
		),		
)); ?>
