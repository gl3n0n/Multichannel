<?php
/* @var $this CouponController */
/* @var $model Coupon */
/* @var $form CActiveForm */
/* */
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'coupon-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
	'htmlOptions' => array('enctype' => 'multipart/form-data'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php
	    foreach(Yii::app()->user->getFlashes() as $key => $message) {
	        echo '<div class="flash-' . $key . '">' . $message . "</div>\n";
	    }
	?>

	<?php echo $form->errorSummary($model); ?>
	

<?php if($model->scenario === 'insert'): // These are displayed when user is creating a new coupon. ?>

    <?php if(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert'): ?>
    <div class="row">
        <?php echo $form->labelEx($model,'ClientId'); ?>
        <?php $this->renderPartial('application.components.view.checkboxlist_g', array(
            'model'=>$model,
            'classname'=>get_class($model),
            'listData'=>$client_list,
            'attributeName'=>'ClientId',
        )); ?>
        <?php echo $form->error($model,'ClientId'); ?>
    </div>
    <?php endif;?>

    <div class="row">
        <?php echo $form->labelEx(Brands::model(),'BrandId'); ?>
        <?php $this->renderPartial('application.components.view.checkboxlist_g', array(
            'model'=>Brands::model(),
            'classname'=>get_class($model),
            'listData'=>(Yii::app()->user->AccessType === 'SUPERADMIN' && $model->scenario === 'insert') ? array() : $brand_list,
            'attributeName'=>'BrandId',
        )); ?>
        <?php echo $form->error($model,'BrandId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx(Campaigns::model(),'CampaignId'); ?>
        <?php $this->renderPartial('application.components.view.checkboxlist_g', array(
            'model'=>$model,
            'classname'=>get_class($model),
            'listData'=>array(),
            'attributeName'=>'CampaignId',
        )); ?>
        <?php echo $form->error($model,'CampaignId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx(Channels::model(),'ChannelId'); ?>
        <?php $this->renderPartial('application.components.view.checkboxlist_g', array(
            'model'=>$model,
            'classname'=>get_class($model),
            'listData'=>array(),
            'attributeName'=>'ChannelId',
        )); ?>
        <?php echo $form->error(Channels::model(),'ChannelId'); ?>
    </div>

    <div class="row">
        <?php echo $form->labelEx($model,'Image Path'); ?>
        <?php echo $form->fileField($model,'Image', array('class'=>'input-file')); ?>
        <?php echo $form->error($model,'Image'); ?>
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
                'size'=>11,'maxlength'=>11, 
                'disabled'=>($model->scenario==='update') ? true: false)
            ); ?>
		<?php echo $form->error($model,'LimitPerUser'); ?>
	</div>

	
	<div class="row">
		<?php echo $form->labelEx($model,'CouponMode'); ?>
        <?php if($model->scenario==='insert'): ?>
    		<label><input type="radio" name="Coupon[CouponMode]" value="system" id="Coupon_CouponMode_Sys" checked> System-generated</label>
    		<label><input type="radio" name="Coupon[CouponMode]" value="user" id="Coupon_CouponMode_Usr"> User Generated</label>
        <?php else: ?>
        <?php echo $form->textField($model,'CouponMode',
            array( // Editable only on update
                'size'=>20,'maxlength'=>11, 
                'disabled'=>true)
        ); ?>
        <?php endif; ?>

	</div>

<?php if($model->scenario==='insert'): ?>
	<div class="row system-generated">
		<?php echo $form->labelEx($model,'CodeLength'); ?>
		<?php echo $form->textField($model,'CodeLength',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo $form->error($model,'CodeLength'); ?>
	</div>
	<div class="row system-generated">
		<?php echo $form->labelEx($model,'Type'); ?>
		<?php echo ZHtml::enumDropDownList(Coupon::model(), 'Type', array(
    'id'=>'Coupon_Type',
    'name'=>'Coupon[Type]',
    'value'=>'',
)); ?>
		<?php echo $form->error($model,'Type'); ?>
	</div>

	<div class="row system-generated">
		<?php echo $form->labelEx($model,'Source'); ?>
		<?php echo $form->textField($model,'Source',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'Source'); ?>
	</div>

	<div class="row system-generated">
		<?php echo $form->labelEx($model,'Quantity'); ?>
		<?php echo $form->textField($model,'Quantity',array('size'=>11,'maxlength'=>11)); ?>
		<?php echo $form->error($model,'Quantity'); ?>
	</div>

	
	
	<div class="row user-generated" style="display:none">
		<?php echo $form->labelEx($model,'File'); ?>		
		<?php echo $form->fileField($model,'File', array('class'=>'input-file')); ?>
		<?php echo $form->error($model,'File'); ?>

	</div>
<?php else: ?>
    <?php if($model->CouponMode==='system') { ?>

    <div class="row system-generated">
        <?php echo $form->labelEx($model,'CodeLength'); ?>
        <?php echo $form->textField($model,'CodeLength',array('size'=>20,'maxlength'=>20,'disabled'=>true)); ?>
        <?php echo $form->error($model,'CodeLength'); ?>
    </div>
    <div class="row system-generated">
        <?php echo $form->labelEx($model,'Type'); ?>
        <?php echo $form->textField($model,'Type',array('size'=>20,'maxlength'=>20,'disabled'=>true)); ?>
        <?php echo $form->error($model,'Type'); ?>
    </div>

    <div class="row system-generated">
        <?php echo $form->labelEx($model,'Source'); ?>
        <?php echo $form->textField($model,'Source',array('size'=>50,'maxlength'=>50,'disabled'=>true)); ?>
        <?php echo $form->error($model,'Source'); ?>
    </div>

    <div class="row system-generated">
        <?php echo $form->labelEx($model,'Quantity'); ?>
        <?php echo $form->textField($model,'Quantity',array('size'=>11,'maxlength'=>11)); ?>
        <?php echo $form->error($model,'Quantity'); ?>
    </div>

    <?php } else if($model->CouponMode==='user') { ?>

    <div class="row user-generated">
        <?php echo $form->labelEx($model,'Current File'); ?>        
        <?php echo $form->textField($model,'File',array('size'=>110,'maxlength'=>11,'disabled'=>true)); ?>
        <?php echo $form->labelEx($model,'File'); ?>        
        <?php echo $form->fileField($model,'File', array('class'=>'input-file')); ?>
        <?php echo $form->error($model,'File'); ?>

    </div>

    <?php } ?>
<?php endif; ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

<?php if($model->scenario==='insert'): ?>
<script>

var Coupons = function() {
    var self = this;
    
    self.moduleName = "Coupon";
    
    self.itemPattern  = "#{mod}_{fld}_Container input[id^={mod}_{fld}_]".replace(/\{mod\}/g, self.moduleName);
    self.rewardItem   = "#{mod}_RewardId".replace(/\{mod\}/g, self.moduleName);
    self.clientItem   = self.itemPattern.replace(/\{fld\}/g, "ClientId");
    self.brandItem    = self.itemPattern.replace(/\{fld\}/g, "BrandId");
    self.campaignItem = self.itemPattern.replace(/\{fld\}/g, "CampaignId");
    self.channelItem  = self.itemPattern.replace(/\{fld\}/g, "ChannelId");
    
    self.lstContainerPtrn = "#{mod}_{fld}_Container".replace(/\{mod\}/g, self.moduleName);
    self.clientContainer   = self.lstContainerPtrn.replace(/\{fld\}/g, "ClientId");
    self.brandContainer    = self.lstContainerPtrn.replace(/\{fld\}/g, "BrandId");
    self.campaignContainer = self.lstContainerPtrn.replace(/\{fld\}/g, "CampaignId");
    self.channelContainer  = self.lstContainerPtrn.replace(/\{fld\}/g, "ChannelId");
    
    self.init = function() {
        self.addEvents();
    };
    
    self.addEvents = function() {
        jQuery(self.clientItem).off().on("click", function() {
            var selection = self.selection("ClientId");
            self.brandList(selection);
        });
        
        jQuery(self.brandItem).off().on("click", function() {
            var selection = self.selection("BrandId");
            self.campaignList(selection);
        });

        jQuery(self.campaignItem).off().on("click", function() {
            var selBrands = self.selection("BrandId");
            var selCampaigns = self.selection("CampaignId");
            self.channelList(selBrands, selCampaigns);
        });
    };
    
    self.selection = function(field) {
        var checkedItems = self.itemPattern.replace(/\{fld\}/g, field) + ":checked";
        return jQuery(checkedItems).map( function() { return this.value; }).get();
    };
    
    self.brandList = function(client) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getbrands",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            ClientId: client
        };
        requestObj.beforeSend = function() {
            jQuery(self.brandContainer).html("Loading...");
            jQuery(self.campaignContainer).empty();
            jQuery(self.channelContainer).empty();
        };
        requestObj.success = function(response) {
            self.refreshList("BrandId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.campaignList = function(brand) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getcampaigns",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            BrandId: brand
        };
        requestObj.beforeSend = function() {
            jQuery(self.campaignContainer).html("Loading...");
            jQuery(self.channelContainer).empty();
        };
        requestObj.success = function(response) {
            self.refreshList("CampaignId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.channelList = function(brand, campaign) {
        var requestObj = {
            url: BaseUrl + "rewardDetails/getchannels",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            BrandId: brand,
            CampaignId: campaign
        };
        requestObj.beforeSend = function() {
            jQuery(self.channelContainer).html("Loading...");
        };
        requestObj.success = function(response) {
            self.refreshList("ChannelId", response);
        };
        jQuery.ajax(requestObj);
    };
    
    self.refreshList = function(field, data) {
        var container = self.lstContainerPtrn.replace(/\{fld\}/g, field);
        jQuery(container).empty();
        
        if(!data) jQuery(container).html("--No data--");
        
        jQuery.each(data, function(idx, val) {
          var itemContainer = jQuery("<div/>");
   
          itemContainer
            .append( // make checkbox
              jQuery("<input>", { type: "checkbox", id: self.moduleName + "_" + field + "_" + idx,
                value: idx, name: self.moduleName + "[" + field + "][]" })
            )
            .append("&nbsp;")
            .append( // make label
              jQuery("<label/>", { "for": self.moduleName + "_" + field + "_" + idx, text: val })
                .css("display", "inline-block")
            )

          jQuery(container).append(itemContainer);
        });

        self.addEvents();


    };
}

var coupon = new Coupons();
coupon.init();

// Show/Hide related fields when selecting the Coupon mode.
$("input[id^=Coupon_CouponMode]").off();
$("input[id^=Coupon_CouponMode]").on('click', function(){
  if(this.value === "system") {
    $(".user-generated").css({"display": "none"});
    $(".system-generated").css({"display": "block"});
  }
  else if(this.value === "user") {
    $(".system-generated").css({"display": "none"});
    $(".user-generated").css({"display": "block"});
  }
});

</script>
<?php endif; ?>
</div><!-- form -->