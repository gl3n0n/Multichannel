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
	'id'=>'couponsystem-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),

)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<?php if($model->scenario === 'insert'): ?>

	<div class="row">
		<?php echo $form->labelEx($model,  'CouponName'); ?>
		<?php echo $form->textField($model,'CouponName',array(
		'style'    => 'width:200px;',
		'maxlength'=> 100
		)); 
		?>
		<?php echo $form->error($model,'CouponName'); ?>
	</div>
	<div class="row">
	<?php echo $form->labelEx($model,'PointsId'); ?>
	<?php echo $form->dropDownList($model,'PointsId',$points_id,
		array(
		    'style'     => 'width:200px;',
		    //'onChange'  => 'getBrands()',
		    'options'   => array("$model->PointsId" => array('selected'=>true)),
		    'prompt'    => '-- Pls Select --',
		),
		array('empty' => '-- Pls Select --')); 
		?>
	<?php echo $form->error($model,'PointsId'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'Image Path'); ?>
		<?php echo $form->fileField($model,'Image', array('class'=>'input-file')); ?>
		<?php echo $form->error($model,'Image'); ?>
	</div>
	
	<div class="row">
		<?php echo $form->labelEx($model,'QRCode Url'); ?>
		<?php echo $form->textField($model,'CouponUrl',array('size'=>255,
					 'style'    => 'width:300px;','maxlength'=>255)); ?>
		<?php echo $form->error($model,'CouponUrl'); ?>
	</div>
	

<?php endif; // End Create scenario ?>

	<div class="row">
		<?php echo $form->labelEx($model,'ExpiryDate'); ?>
		<?php
			$this->widget('zii.widgets.jui.CJuiDatePicker', array(
		   		'model'=>$model,
                'value'=>$model->ExpiryDate,
				'attribute'=>'ExpiryDate',
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
		<?php echo $form->error($model,'ExpiryDate'); ?>
	</div>
	
	
	<div class="row">
		<?php echo $form->labelEx($model,'LimitPerUser'); ?>
		<?php echo $form->textField($model,'LimitPerUser',
            array( // Editable only on update
                'size'      =>11,'maxlength'=>11, 
		 'style'    => 'width:200px;',
                'disabled'=>($model->scenario==='update') ? true: false)
            ); ?>
		<?php echo $form->error($model,'LimitPerUser'); ?>
	</div>

	
	<div class="row">
		<?php echo $form->labelEx($model,'CouponMode'); ?>
        <?php if($model->scenario==='insert'): ?>
    		<label><input type="radio" 
				name="CouponSystem[CouponMode]" 
				value="system" 
				id="CouponSystem_CouponMode" <?php echo ($model->CouponMode != 'user')?("checked"):("");?>
				/> System-generated</label>
    		<label><input type="radio" 
				name="CouponSystem[CouponMode]" 
				value="user" 
				id="CouponSystem_CouponMode" <?php echo ($model->CouponMode == 'user')?("checked"):("");?>
				/> User Generated</label>
        <?php else: ?>
        <?php echo $form->textField($model,'CouponMode',
            array( // Editable only on update
                'size'      =>20,'maxlength'=>11, 
		'style'     => 'width:200px;',
                'disabled'  =>true)
        ); ?>
        <?php endif; ?>

	</div>

<?php if($model->scenario==='insert'): ?>
	<div class="row system-generated">
		<?php echo $form->labelEx($model,'CodeLength'); ?>
		<?php echo $form->textField($model,'CodeLength',
				array('size'=>20,
					'style'    => 'width:200px;',
					'maxlength'=>20)); ?>
		<?php echo $form->error($model,'CodeLength'); ?>
	</div>

   <div class="row system-generated">
		<?php echo $form->labelEx($model,'Type'); ?>
		<?php echo ZHtml::enumDropDownList(CouponSystem::model(), 'Type', array(
		    'id'   =>'Type',
		    'options' => array("$model->Type" => array('selected'=>true)),
		    'name' =>'CouponSystem[Type]',
		    'style'=> 'width:200px;',
		    'value'=>'',
		)); ?>
		<?php echo $form->error($model,'Type'); ?>
       </div>

	<div class="row system-generated">
		<?php echo $form->labelEx($model,'Source'); ?>
		<?php echo $form->textField($model,'Source',array('size'=>50,
					 'style'    => 'width:200px;','maxlength'=>50)); ?>
		<?php echo $form->error($model,'Source'); ?>
	</div>

	<div class="row system-generated">
		<?php echo $form->labelEx($model,'Quantity'); ?>
		<?php echo $form->textField($model,'Quantity',array('size'=>11,
					 'style'    => 'width:200px;','maxlength'=>11)); ?>
		<?php echo $form->error($model,'Quantity'); ?>
	</div>

	
	<div class="row user-generated" style="display:none">
		<?php echo $form->labelEx($model,'File'); ?>		
		<?php echo $form->fileField($model,'File', array('class'=>'input-file')); ?>
		<?php echo $form->error($model,'File'); ?>

	</div>
    <div class="row">
		<?php echo $form->labelEx($model,'CouponType'); ?>
		<?php echo ZHtml::enumDropDownList(CouponSystem::model(), 'CouponType', array(
		    'id'   =>'CouponType',
		    'name' =>'CouponSystem[CouponType]',
		    'options' => array("$model->CouponType" => array('selected'=>true)),
		    'style'   => 'width:200px;',
		)); ?>
		<?php echo $form->error($model,'CouponType'); ?>
	</div>
         <div class="row">
         	<div id="PointsValue_Container" style="display:none">
		<?php echo $form->labelEx($model,  'PointsValue'); ?>
		<?php echo $form->textField($model,'PointsValue',
				array('size'=>20,
					'style'    => 'width:200px;',
					'maxlength'=>20)); ?>
		<?php echo $form->error($model,'PointsValue'); ?>
		</div>
	</div>

<?php else: ?>
    <?php if($model->CouponMode==='system') { ?>

    <div class="row system-generated">
        <?php echo $form->labelEx($model,'CodeLength'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20, 'style'    => 'width:200px;','maxlength'=>20,'disabled'=>true)); ?>
        <?php echo $form->error($model,'CodeLength'); ?>
    </div>

   <div class="row system-generated">
		<?php echo $form->labelEx($model,'Type'); ?>
		<?php echo ZHtml::enumDropDownList(CouponSystem::model(), 'Type', array(
		    'id'   =>'Type',
		    'name' =>'CouponSystem[Type]',
		    'style'=> 'width:200px;',
		    'options' => array("$model->Type" => array('selected'=>true)),
		    'value'=>'',
		)); ?>
		<?php echo $form->error($model,'Type'); ?>
       </div>


    <div class="row system-generated">
        <?php echo $form->labelEx($model,'Source'); ?>
        <?php echo $form->textField($model,'Source',array('size'=>50, 'style'    => 'width:200px;','maxlength'=>50,'disabled'=>true)); ?>
        <?php echo $form->error($model,'Source'); ?>
    </div>

    <div class="row system-generated">
        <?php echo $form->labelEx($model,'Quantity'); ?>
        <?php echo $form->textField($model,'Quantity',array('size'=>11, 'style'    => 'width:200px;','maxlength'=>11)); ?>
        <?php echo $form->error($model,'Quantity'); ?>
    </div>

    <?php } else if($model->CouponMode==='user') { ?>

    <div class="row user-generated">
        <?php echo $form->labelEx($model,'Current File'); ?>        
        <?php echo $form->textField($model,'File',array('size'=>110, 'style' => 'width:200px;','maxlength'=>11,'disabled'=>true)); ?>
        <?php echo $form->labelEx($model,  'File'); ?>        
        <?php echo $form->fileField($model,'File', array('class'=>'input-file')); ?>
        <?php echo $form->error($model,'File'); ?>
    </div>

    <?php } ?>
<?php endif; ?>
<?php if($model->scenario==='update'): ?>
	<div class="row">
        <?php echo $form->labelEx($model,'CouponUrl'); ?>
        <?php echo $form->textField($model,'CouponUrl',array('size'=>255, 'style'=> 'width:300px;','maxlength'=>255,'disabled'=>true)); ?>
        <?php echo $form->error($model,'CouponUrl'); ?>
    </div>
	
	<div class="row">
        <?php echo $form->labelEx($model,'CouponType'); ?>
        <?php echo $form->textField($model,'CouponType',array('size'=>11, 'style'=> 'width:200px;','maxlength'=>11,'disabled'=>true)); ?>
        <?php echo $form->error($model,'CouponType'); ?>
    </div>
	

	 <?php if($model->CouponType==='CONVERT_TO_POINTS' || $model->CouponType==='EXCHANGE_POINTS_TO_COUPON') { ?>
		<div class="row">
                <?php echo $form->labelEx($model,'PointsValue'); ?>
		<?php echo $form->textField($model,'PointsValue',array('size'=>30, 'style'=> 'width:400px;','maxlength'=>30)); ?>
		<?php echo $form->error($model,'PointsValue'); ?>
    		</div>
        <?php } ?>

	<div class="row">
	    <?php echo $form->labelEx($model,'Status'); ?>
	    <?php echo CHtml::dropDownList('CouponSystem[Status]', 
		$model->scenario === 'update' ? $model->Status : 'ACTIVE', 
		ZHtml::enumItem($model, 'Status'),array( 'style'    => 'width:200px;')); 
	    ?>
	    <?php echo $form->error($model,'Status'); ?>
	</div>
<?php endif; ?>
	 
	 <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<?php if($model->scenario==='insert'): ?>
<script>

//dynamic loading
$( document ).ready(function() {
	
	$("#CouponType").change(function(){
	    
		var choice = $("#CouponType").val();

		//chk	PointsValue
    		$("#PointsValue_Container").css({"display": "none"});
		if(choice == 'CONVERT_TO_POINTS' || choice == 'EXCHANGE_POINTS_TO_COUPON')
		{
    			$("#PointsValue_Container").css({"display": "block"});
		}
	});
	if("<?=$model->CouponType?>" == "CONVERT_TO_POINTS" || "<?=$model->CouponType?>" == "EXCHANGE_POINTS_TO_COUPON")
	{
    		$("#PointsValue_Container").css({"display": "block"});
	}
});
// Show/Hide related fields when selecting the Coupon mode.
$("input[id^=CouponSystem_CouponMode]").off();
$("input[id^=CouponSystem_CouponMode]").on('click', function(){
  if(this.value === "system") {
    $(".user-generated").css({"display": "none"});
    $(".system-generated").css({"display": "block"});
  }
  else if(this.value === "user") {
    $(".system-generated").css({"display": "none"});
    $(".user-generated").css({"display": "block"});
  }
});

if("<?php echo $model->CouponMode?>" === "system")
{
    $(".user-generated").css({"display": "none"});
    $(".system-generated").css({"display": "block"});
}
if("<?php echo $model->CouponMode?>" === "user")
{
    $(".system-generated").css({"display": "none"});
    $(".user-generated").css({"display": "block"});
}


</script>

<?php endif; ?>
