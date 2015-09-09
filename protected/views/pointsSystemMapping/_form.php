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
	'id'=>'pointssystemmapping-form',
	// Please note: When you enable ajax validation, make sure the corresponding
	// controller action is handling ajax validation correctly.
	// There is a call to performAjaxValidation() commented in generated controller code.
	// See class documentation of CActiveForm for details on this.
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>


	<div class="row">
		<?php echo $form->labelEx($model,'PointsId'); ?>
		
        	
		<?php 
			$pIdx = '';
			if($model->PointsId && $model->ClientId)
				$pIdx = sprintf("%s-%s",$model->PointsId,$model->ClientId);
			echo $form->dropDownList($model,'PointsId',$points_system_list, array(
				'empty'   => '--Select Points System--',
				'options' => array("$pIdx" => array('selected'=>true)),
				'onchange'=> "javascript:multi.refreshListBRANDS();",
			));?>
		<?php echo $form->error($model,'PointsId'); ?>
	</div>

	<div class="row">
	   <?php echo $form->labelEx($model,'BrandId'); ?>
	    <?php $this->renderPartial('application.components.view.checkboxlist', array(
	      'model'        => $model,
	      'listData'     => $brand_list,
	      'attributeName'=> 'BrandId',
	    )); ?>	    
	    <?php echo $form->error($model,'BrandId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'CampaignId'); ?>
	    <?php $this->renderPartial('application.components.view.checkboxlist', array(
	      'model'        => $model,
	      'listData'     => $campaign_list,
	      'attributeName'=> 'CampaignId',
	    )); ?>
	    <?php echo $form->error($model,'CampaignId'); ?>
	  </div>

	  <div class="row">
	    <?php echo $form->labelEx($model,'ChannelId'); ?>
	    <?php $this->renderPartial('application.components.view.checkboxlist', array(
	      'model'        => $model,
	      'listData'     => $channel_list,
	      'attributeName'=> 'ChannelId',
	    )); ?>
	    <?php echo $form->error($model,'ChannelId'); ?>
	  </div>

	 
	 <div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->

<script type="text/javascript">


//dynamic loading
$( document ).ready(function() {
});


var PointsSystemMapping = function() {
    var self = this;
    
    self.moduleName = "PointsSystemMapping";
    
    self.itemPattern  = "#{mod}_{fld}_Container input[id^={mod}_{fld}_]".replace(/\{mod\}/g, self.moduleName);
    self.pointItem    = self.itemPattern.replace(/\{fld\}/g, "PointsId");
    self.brandItem    = self.itemPattern.replace(/\{fld\}/g, "BrandId");
    self.campaignItem = self.itemPattern.replace(/\{fld\}/g, "CampaignId");
    self.channelItem  = self.itemPattern.replace(/\{fld\}/g, "ChannelId");
    
    self.lstContainerPtrn = "#{mod}_{fld}_Container".replace(/\{mod\}/g, self.moduleName);
    self.pointContainer   = self.lstContainerPtrn.replace(/\{fld\}/g,  "PointsId");
    self.brandContainer    = self.lstContainerPtrn.replace(/\{fld\}/g, "BrandId");
    self.campaignContainer = self.lstContainerPtrn.replace(/\{fld\}/g, "CampaignId");
    self.channelContainer  = self.lstContainerPtrn.replace(/\{fld\}/g, "ChannelId");
    
    self.init = function() {
        self.addEvents();
    };
    
    self.addEvents = function() {
        jQuery(self.pointItem).off().on("click", function() {
            var selection = self.selection("PointsId");
            self.brandList(selection);
        });
        
        jQuery(self.brandItem).off().on("click", function() {
            var selection = self.selection("BrandId");
            self.campaignList(selection);
        });

        jQuery(self.campaignItem).off().on("click", function() {
            var selBrands    = self.selection("BrandId");
            var selCampaigns = self.selection("CampaignId");
            self.channelList(selBrands, selCampaigns);
        });

        jQuery(self.pointItem).off().on("change", function() {
            var selection = self.selection("PointsId");
            self.brandList(selection);
        });
        
        jQuery(self.brandItem).off().on("change", function() {
            var selection = self.selection("BrandId");
            self.campaignList(selection);
        });

        jQuery(self.campaignItem).off().on("change", function() {
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
            url: BaseUrl + "pointsSystemMapping/getbrands",
            type: "GET",
            dataType: "json"
        };
        requestObj.data = {
            PointsId: client
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
            url: BaseUrl + "pointsSystemMapping/getcampaigns",
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
            url: BaseUrl + "pointsSystemMapping/getchannels",
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

    self.refreshListBRANDS = function() {
        //var selection = self.selection("PointsId");
        var selection = $('#PointsSystemMapping_PointsId').val();
        self.brandList(selection);
    }

}

var multi = new PointsSystemMapping();
multi.init();



</script>
