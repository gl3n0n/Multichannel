<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
echo Yii::app()->params['jQueryInclude'];
?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'my-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>


	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); 
	?>

	<?php
	echo CHtml::hiddenField('CustomerSubscriptions[CustomerId]', ( $CustomerId!=null ? $CustomerId : 0 ), array('id'=>'CustomerSubscriptions[CustomerId]'));
	?>

	
	<div class="row">
		<?php echo $form->labelEx($model,'BrandId'); ?>
		<?php echo $form->dropDownList($model,'BrandId',$brand_list?$brand_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'onChange'=> 'loadCampaignsList()',
        	    'options' => array("$model->BrandId" => array('selected'=>true)),
        	    'prompt'    => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'BrandId'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php echo $form->dropDownList($model,'CampaignId',$campaign_list?$campaign_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->CampaignId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --'));
        	?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->dropDownList($model,'PointsId',$point_list?$point_list:array(),
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->PointsId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'PointsValue'); ?>
		<?php
		echo CHtml::textField('CustomerSubscriptions[PointsValue]',Yii::app()->request->getParam('CustomerSubscriptions[PointsValue]'), array(
				     'style'   => 'width:200px;',
				     'default' => Yii::app()->request->getParam('CustomerSubscriptions[PointsValue]'),
				     'id'    => 'CustomerSubscriptions[PointsValue]'));
		?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Add Points',array(
				     'style' => 'width:95px;',
				     'name'  => 'btnAdd',
				     'id'    => 'btnAdd'
				     )); ?>
		&nbsp;
		<?php echo CHtml::submitButton('Deduct Points',array(
				     'style' => 'width:95px;',
				     'name'  => 'btnSub',
				     'id'    => 'btnSub')); ?>
	</div>
<?php $this->endWidget(); ?>

</div><!-- form -->

<script>


//dynamic loading
$( document ).ready(function() {
	

	$("#CustomerSubscriptions_ClientId").change(function(){
	 	loadBrandsList();
 	     
	});
	
	function loadBrandsList()
	{
		var url = BaseUrl + "channels/getbrands/?ClientId=" + $("#CustomerSubscriptions_ClientId" ).val();

		    loadlist($('select#CustomerSubscriptions_BrandId').get(0),
			url,
			''
		)
	}
	
	$("#CustomerSubscriptions_BrandId").change(function(){
	    loadCampaignsList();
	});
	
	function loadCampaignsList()
	{
		var url = BaseUrl + "channels/getcampaigns/?BrandId=" + $("#CustomerSubscriptions_BrandId" ).val();
		loadlist($('select#CustomerSubscriptions_CampaignId').get(0),
			url,
			''
		);
	}
	//add it
	function loadlist(selobj,url,nameattr)
	{
	    $(selobj).empty();
	    $(selobj).append(
		$('<option></option>')
		.val('')
		.html('-- Please Select --'));
	    $.getJSON(url,{},function(data)
	    {
	        $.each(data, function(i,obj)
	        {
	            $(selobj).append(
	                 $('<option></option>')
	                        .val(i)
	                        .html(obj));
	        });
	 });
}
});

</script>
