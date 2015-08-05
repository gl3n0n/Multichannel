<?php
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'rewards-list-form',
    // Please note: When you enable ajax validation, make sure the corresponding
    // controller action is handling ajax validation correctly.
    // There is a call to performAjaxValidation() commented in generated controller code.
    // See class documentation of CActiveForm for details on this.
    'enableAjaxValidation'=>false,
    'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php echo $form->errorSummary($model); ?>

    <div class="row">
        <?php echo CHtml::label('Brand', 'Reports_BrandId'); ?>
        <?php echo $form->dropDownList($model, 'BrandId', $brands); ?>
        <?php echo $form->error($model,'BrandId'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('Campaign', 'Reports_CampaignId'); ?>
        <?php echo $form->dropDownList($model, 'CampaignId', $campaigns); ?>
        <?php echo $form->error($model,'CampaignId'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('Channel', 'Reports_ChannelId'); ?>
        <?php echo $form->dropDownList($model, 'ChannelId', $channels); ?>
        <?php echo $form->error($model,'ChannelId'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('Customer', 'Reports_CustomerId'); ?>
        <?php echo $form->dropDownList($model, 'CustomerId', $customers); ?>
        <?php echo $form->error($model,'CustomerId'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('Form Date','Reports_DateFrom'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'attribute'=>'DateFrom',
           // additional javascript options for the date picker plugin
                'options' => array(
                    'showAnim' => "slideDown",
                    'changeMonth' => true,
                    'numberOfMonths' => 1,
                    'showOn' => "button",
                    'buttonImageOnly' => false,
                    'dateFormat' => "yy-mm-dd",
                    'showButtonPanel' => true      
                )
            ));
        ?>
        <?php echo $form->error($model,'DateFrom'); ?>
    </div>

    <div class="row">
        <?php echo CHtml::label('To Date','Reports_DateFrom'); ?>
        <?php
            $this->widget('zii.widgets.jui.CJuiDatePicker', array(
                'model'=>$model,
                'attribute'=>'DateTo',
           // additional javascript options for the date picker plugin
                'options' => array(
                    'showAnim' => "slideDown",
                    'changeMonth' => true,
                    'numberOfMonths' => 1,
                    'showOn' => "button",
                    'buttonImageOnly' => false,
                    'dateFormat' => "yy-mm-dd",
                    'showButtonPanel' => true      
                )
            ));
        ?>
        <?php echo $form->error($model,'DateTo'); ?>
    </div>

    <div class="row buttons">
        <?php //echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); 
            echo CHtml::submitButton('Create');
        ?>
    </div>

<?php $this->endWidget(); ?>
</div><!-- form -->