<?php
/* @var $this PointsLogController */
/* @var $model PointsLog */
/* @var $form CActiveForm */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'points-log-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'CustomerId'); ?>
		<?php echo $form->dropDownList($model,'CustomerId',$customer_list,array(
		      'prompt' => '--Select a Customer--',
		    )); ?>
		<?php echo $form->error($model,'CustomerId'); ?>

	</div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'BrandId'); ?>
	    <?php echo $form->dropDownList($model,'BrandId',$brand_list,array(
	      'prompt' => '--Select a brand--',
	    )); ?>
	    <?php echo $form->error($model,'BrandId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'CampaignId'); ?>
	    <?php echo $form->dropDownList($model,'CampaignId',$campaign_list,
		    array(
			'prompt' => '--Select a Campaign--',
		    )    
	    ); 
	    ?>
	    <?php echo $form->error($model,'CampaignId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'ChannelId'); ?>
	    <?php echo $form->dropDownList($model,'ChannelId',$channel_list,
	        array(
	    		'prompt' => '--Select a Channel--',
		    )    
	    ); ?>
	    <?php echo $form->error($model,'ChannelId'); ?>
	  </div>
	  
	<div class="row">
		<?php echo $form->labelEx($model,'Points'); ?>
		<?php echo $form->textField($model,'Points'); ?>
		<?php echo $form->error($model,'Points'); ?>
	</div>
  
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
	<?php echo $form->hiddenField($model,'PointsId',array('value'=>'0')); ?>
<?php $this->endWidget(); ?>

</div><!-- form -->