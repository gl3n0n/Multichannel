<?php
/* @var $this BrandsController */
/* @var $model Brands */
/* @var $form CActiveForm */
if($model->scenario === 'insert')
{
   echo Yii::app()->params['jQueryInclude'];
}


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

	<?php echo $form->errorSummary($model); ?>

    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->dropDownList($model,'ClientId',$client_list,
        	array(
        	    'style'     => 'width:200px;',
        	    'onChange'  => 'getBrands()',
        	    'prompt'    => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif; ?>


	<div class="row">
		<?php echo $form->labelEx($model,'BrandId'); ?>
		<?php echo $form->dropDownList($model,'BrandId',$brand_list,
		array(
        	    'style'   => 'width:200px;',
        	    'onChange'=> 'getCampaigns()',
        	    'options' => array("$model->BrandId" => array('selected'=>true)),
        	    'prompt'    => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
		<?php echo $form->error($model,'BrandId'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php echo $form->dropDownList($model,'CampaignId',$campaign_list,
		array(
        	    'style'   => 'width:200px;',
        	    'onChange'=> 'getChannels()',
        	    'options' => array("$model->CampaignId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --'));
        	?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'ChannelId'); ?>
		<?php echo $form->dropDownList($model,'ChannelId',$channel_list,
		array(
        	    'style'   => 'width:200px;',
        	    'options' => array("$model->CampaignId-$model->ChannelId" => array('selected'=>true)),
        	    'prompt'  => '-- Pls Select --',
        	),
        	array('empty' => '-- Pls Select --')); 
        	?>
        	
		<?php echo $form->error($model,'ChannelId'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Title'); ?>
		<?php echo $form->textField($model,'Title',array(
		'style'    => 'width:200px;',
		'maxlength'=>255
		)); 
		?>
		<?php echo $form->error($model,'Title'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Description'); ?>
		<?php echo $form->textField($model,'Description',array(
			'style' => 'width:200px;',
			'maxlength'=>255
		));
		?>
		<?php echo $form->error($model,'Description'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'EventDate'); ?>
		<?php
	    $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name'  => 'EventDate',
           'value' => substr($model->EventDate,0,10),
	   'model'=>$model,
	   'attribute'=>'EventDate',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
           )
       ));	?>
		<?php echo $form->error($model,'EventDate'); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script>


//dynamic loading
$( document ).ready(function() {

	
	$("#ScheduledPost_ClientId").change(function(){
	    
	    var url = BaseUrl + "channels/getbrands/?ClientId=" + $("#ScheduledPost_ClientId" ).val();;
	    
	    loadlist($('select#ScheduledPost_BrandId').get(0),
		url,
		''
 	     );
 	     
	});
	$("#ScheduledPost_BrandId").change(function(){
	    
	    var url = BaseUrl + "channels/getcampaigns/?BrandId=" + $("#ScheduledPost_BrandId" ).val();;
	    
	    loadlist($('select#ScheduledPost_CampaignId').get(0),
	    		url,
	    		''
 	     );
	    
	    
	});
	$("#ScheduledPost_CampaignId").change(function(){
	    var url = BaseUrl + "channels/getchannels?CampaignId="  + 
	    	$("#ScheduledPost_CampaignId" ).val() + '&BrandId=' + 
	    	$("#ScheduledPost_BrandId" ).val();
	    loadlist($('select#ScheduledPost_ChannelId').get(0),
		url,
		''
		);
	});
	$("#ScheduledPost_ChannelId").change(function(){
	    
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