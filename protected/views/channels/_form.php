<?php
/* @var $this ChannelsController */
/* @var $model Channels */
/* @var $form CActiveForm */
if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert') {
    $clients_model = Clients::model()->active()->findAll();
    $client_list = CHtml::listData($clients_model, 'ClientId', 'CompanyName');
}
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'channels-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php if($model->scenario === 'insert'): ?>
    <p class="note">Select a Brand to get the available Campaigns.<br>You can select multiple Campaigns.</p>
<?php endif; ?>

	<?php echo $form->errorSummary($model); ?>

    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $form->dropDownList($model,'ClientId',$client_list, array(
            'empty'=>'--Select Client--',
            'ajax'=>array('type'=>'get',
                'url'=>$this->createUrl('getbrands'),
                // 'update'=>'#' . get_class($model) . '_CampaignId',
                'data'=>'js:{"ClientId" : this.value }',
                'success'=>'js:function data(response) {
                    $("#' . get_class($model) . '_BrandId'.'").html("");
                    $("#' . get_class($model) . '_CampaignId'.'").html($("<option/>", {value: "", text: "--Select a client--" })).attr("size", 1);

                    var curval = $("#' . get_class($model) . '_ClientId'.'").val();

                    if(response.length === 0 || ! curval)
                    {
                        var promptText = "--Select a client--";
                    }
                    else {
                        var promptText = "--Select a brand--";
                        $("#' . get_class($model) . '_CampaignId'.'")
                          .html($("<option/>", {value: "", text: "--Select a brand--" }))
                          .attr("size", 1);
                    }

                    $("#' . get_class($model) . '_BrandId'.'").html($("<option/>", {value: "", text: promptText }));

                    $.each(response, function(idx, val) {
                        $("#' . get_class($model) . '_BrandId'.'").append($("<option/>", {
                            value: idx,
                            text: val
                        }));
                    });
                }',
            ),
        )); ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif; ?>

<?php if($model->scenario === 'insert'): ?>

	<div class="row">
		<?php echo $form->labelEx($model,'BrandId'); ?>
		<?php echo $form->dropDownList($model,'BrandId',$brand_id, array(
            'prompt'=>'--Select a brand--',
            'ajax'=>array('type'=>'get',
                'url'=>$this->createUrl('getcampaigns'),
                // 'update'=>'#' . get_class($model) . '_CampaignId',
                'data'=>'js:{"BrandId" : this.value }',
                'success'=>'js:function data(response) {
                    $("#' . get_class($model) . '_CampaignId'.'").html("").attr("size", 1);

                    var curval = $("#' . get_class($model) . '_BrandId'.'").val();

                    if(response.length === 0)
                    {
                        var promptText = ( ! curval) ? "--Select a brand--" : "--Nothing--";
                        $("#' . get_class($model) . '_CampaignId'.'").html($("<option/>", {value: "", text: promptText })).attr("size",1);
                    }
                    else $("#' . get_class($model) . '_CampaignId'.'").attr("size", 5);

                    $.each(response, function(idx, val) {
                        $("#' . get_class($model) . '_CampaignId'.'").append($("<option/>", {
                            value: idx,
                            text: val
                        }));
                    });
                }',
            ),
        )); ?>
		<?php echo $form->error($model,'BrandId'); ?>
	</div>

    <div class="row">
        <?php echo $form->labelEx($model,'CampaignId'); ?>
        <?//php echo $form->dropDownList($model,'CampaignId',$campaign_id); ?>
        <?php echo CHtml::listBox(get_class($model).'[CampaignId]', array(), 
            array('empty'=>'--Select a brand--'),
            array('id'=>get_class($model) . '_CampaignId', 'multiple'=>'multiple', 'size'=>1)
        ); ?>
        <?php echo $form->error($model,'CampaignId'); ?>
    </div>

<?php else: ?>
    
	<div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php echo $model->channelClients->CompanyName; ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
	
	<div class="row">
        <?php echo $form->labelEx($model,'BrandId'); ?>
        <?php echo $model->channelBrands->BrandName; ?>
        <?php echo $form->error($model,'BrandId'); ?>
    </div>

	<div class="row">
		<?php echo $form->labelEx($model,'CampaignId'); ?>
		<?php echo $model->channelCampaigns->CampaignName; ?>
		<?php echo $form->error($model,'CampaignId'); ?>
	</div>

<?php endif; ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ChannelName'); ?>
		<?php echo $form->textField($model,'ChannelName',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'ChannelName'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Description'); ?>
		<?php echo $form->textField($model,'Description',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'Description'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DurationFrom'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'DurationFrom',
		   'model'=>$model,
			'attribute'=>'DurationFrom',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#DurationTo").datepicker("option", "minDate", selectedDate); }',            
           )
       ));	?>
		<?php echo $form->error($model,'DurationFrom'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'DurationTo'); ?>
		<?php
			 $this->widget('zii.widgets.jui.CJuiDatePicker', array(
           'name' => 'DurationTo',
		   'model'=>$model,
			'attribute'=>'DurationTo',
           // additional javascript options for the date picker plugin
           'options' => array(
               'showAnim' => "slideDown",
               'changeMonth' => true,
               'numberOfMonths' => 1,
               'showOn' => "button",
               'buttonImageOnly' => false,
               'dateFormat' => "yy-mm-dd",
               'showButtonPanel' => true,
               'onClose' => 'js:function(selectedDate) { $("#DurationFrom").datepicker("option", "maxDate", selectedDate); }',
           )
       ));	?>
		<?php echo $form->error($model,'DurationTo'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'Type'); ?>
		<?php echo $form->textField($model,'Type',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'Type'); ?>
	</div>


	<div class="row">
		<?php echo $form->labelEx($model,'Status'); ?>
		<?php echo ZHtml::enumDropDownList(Channels::model(), 'Status', array(
    'id'=>'Channels_Status',
    'name'=>'Channels[Status]',
    'value'=>'',
)); ?>
		<?php echo $form->error($model,'Status'); ?>
	</div>
  

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->