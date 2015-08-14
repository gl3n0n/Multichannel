<?php
/* @var $this CouponController */
/* @var $dataProvider CActiveDataProvider */

$this->breadcrumbs=array(
	'Coupons',
);

$this->menu=array(
	array('label'=>'Create Coupon', 'url'=>array('create')),
);

//overwrite
if(Yii::app()->user->AccessType === "SUPERADMIN")
{
	$this->menu=array(
	array('label'=>'Create Raffle',   'url'=>array('create')),
	array('label'=>'Pending Raffles', 'url'=>array('pending')),
	);
}
?>

<h1>Generate Participants</h1>
<div>
<?php 
if($this->statusMsg != null)
{
    echo "<div class='errorSummary'><p><h5>$this->statusMsg</h5></p></div>";
}
$form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl("raffle/genwinners"),
	'method'=>'get',
)); ?>
	<!--//
	<fieldset>
		<legend>Search By Source</legend>
		<input type="text" id='search' name="search" id="list-search" placeholder="Source" title="Search Source">
		<button type="submit">Search</button>
	</fieldset>
	//-->
<?php $this->endWidget(); ?>
<script>

    // Controll submit form event
    function generateWinners()
    {
    	document.getElementById('mainFrm').submit();
    }


</script>

</div>
<?php
echo CHtml::beginForm(Yii::app()->createUrl("raffle/drawwinner"),
	'post',array('id'=>'mainFrm'));
?>
<input type="hidden" name="show_winner" value="true">
<input type="hidden" name="CouponId"    value="<?php echo $mapping["mdata"]["CouponId"]; ?>">
<input type="hidden" name="NoOfWinners" value="<?php echo $mapping["mdata"]["NoOfWinners"]; ?>">
<input type="hidden" name="RaffleId"    value="<?php echo $mapping["mdata"]["RaffleId"]; ?>">
<?php 
if(0)
{
	echo "<hr>";
	echo @var_export($mapping,true);
	foreach($dataProvider->getData() as $row)
	{
	echo "<hr>";
	echo @var_export($row,true);
	}
	exit;
}

$this->widget('CGridViewEtc', array(
	'id' => 'gen-approve-view',
	'dataProvider'=>$dataProvider,
	//'itemView'=>'_view',
	'etc' => $mapping,
	'columns'=>array(
		array(
		    'name'  => 'Please select the participants for the raffle draw',
		    'type'  => 'raw',
		    'value' => '$this->grid->etcButtonRaffle($data["Email"],$data["CustomerId"])',
		),
	),
)); 


?>
<table class="items">
<thead>
<tr>
	<th id="gen-approx" style="padding-left:30px;">
		<input type='button' onclick='generateWinners();' id='drawWinner' value=' Draw Winner ' style='width:210px;' />
	</th>
</tr>
</thead>
</table>
<?php
echo CHtml::endForm(); 
?>