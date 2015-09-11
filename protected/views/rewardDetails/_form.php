<?php
/* @var $this RewardDetailsController */
/* @var $model RewardDetails */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'reward-details-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>
    <?php if($model->scenario === 'insert'): // These are displayed when user is creating a new reward details. ?>

	 <?php if(Yii::app()->user->AccessType === 'SUPERADMIN'): ?>
	<div class="row">
		<?php echo $form->labelEx($model,'ClientId'); ?>
		<?php echo $form->dropDownList($model,'ClientId',$client_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->ClientId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'ClientId'); ?>
	</div>	
	<?php endif; ?>
	
	<div class="row">
		<?php echo $form->labelEx($model,'RewardId'); ?>
		<?php echo $form->dropDownList($model,'RewardId',$rewards_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->RewardId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'RewardId'); ?>
	</div>	
	
	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		<?php echo $form->dropDownList($model,'PointsId',$rewards_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->PointsId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
        	
		<?php echo $form->error($model,'PointsId'); ?>
	</div>
	<?php else: // End Create scenario ?>	
	
	<div class="row">
		<?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,
        		'maxlength'=>20,
        		'disabled'=>true, 
        		'value'=>$model->byClients->CompanyName,
        		'style' => 'width:200px;')); ?>
        <?php echo $form->error($model,'ClientId'); ?>
	  </div>
	  
	<div class="row">
		<?php echo $form->labelEx($model,'RewardId'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,
        		'maxlength'=>20,
        		'disabled'=>true, 
        		'value'=>$model->byRewards->Title,
        		'style' => 'width:200px;')); ?>
        <?php echo $form->error($model,'RewardId'); ?>
	  </div>
	  
	  <div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,
        			'maxlength'=>20,'disabled'=>true, 
        			'value'    =>$model->byPointsSystem->Name,
        			'style'    => 'width:200px;')); ?>
        <?php echo $form->error($model,'PointsId'); ?>
	  </div>
	<?php endif; // End Create scenario ?>

	<div class="row">
		<?php echo $form->labelEx($model,'Name'); ?>
		<?php echo $form->textField($model,'Name',array('size'=>60,'maxlength'=>255,'style' => 'width:200px;')); ?>
		<?php echo $form->error($model,'Name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Inventory'); ?>
		<?php echo $form->textField($model,'Inventory',array('style' => 'width:200px;')); ?>
		<?php echo $form->error($model,'Inventory'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Limitations'); ?>
		<?php echo $form->textField($model,'Limitations',array('size'=>60,'maxlength'=>255 ,'style' => 'width:200px;')); ?>
		<?php echo $form->error($model,'Limitations'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Value'); ?>
		<?php echo $form->textField($model,'Value',array('size'=>50,'maxlength'=>50 ,'style' => 'width:200px;')); ?>
		<?php echo $form->error($model,'Value'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'StartDate'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'StartDate',
		   'model'=>$model,
			'attribute'=>'StartDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#EndDate").datepicker("option", "minDate", selectedDate); }',            
           )
       ));	?>
		<?php echo $form->error($model,'StartDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'EndDate'); ?>
		<?php
			 $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'EndDate',
		   'model'=>$model,
			'attribute'=>'EndDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#StartDate").datepicker("option", "maxDate", selectedDate); }',
           )
       ));	?>
		<?php echo $form->error($model,'EndDate'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo CHtml::dropDownList('RewardDetails[Status]', 
			$model->scenario === 'update' ? $model->Status : 'ACTIVE', 
			ZHtml::enumItem($model, 'Status')); 
		?>
		<?php echo $form->error($model,'Status'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>


//dynamic loading
$( document ).ready(function() {

	$("#RewardDetails_ClientId").change(function(){
	    
		var choice = $("#RewardDetails_ClientId" ).val();

		//chk	
		var mde  = "ClientId="     + encodeURIComponent(choice) ;
		var url  = BaseUrl         + "rewardDetails/getRewardslist/?" + mde;
		loadlist($('select#RewardDetails_RewardId').get(0),
		url,
		''
		);
	});
	
	$("#RewardDetails_RewardId").change(function(){
	    
		var choice = $("#RewardDetails_RewardId" ).val();

		//chk	
		var mde  = "RewardId="     + encodeURIComponent(choice) ;
		var url  = BaseUrl         + "rewardDetails/getPointSystemlist/?" + mde;
		loadlist($('select#RewardDetails_PointsId').get(0),
		url,
		''
		);
	});


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
