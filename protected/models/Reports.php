<?php

/**
 * ContactForm class.
 * ContactForm is the data structure for keeping
 * contact form data. It is used by the 'contact' action of 'SiteController'.
 */
class Reports extends CFormModel
{
    public $BrandId;
    public $CampaignId;
    public $ChannelId;
    public $CustomerId;
    public $DateFrom;
    public $DateTo;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            array('BrandId, CampaignId, ChannelId, CustomerId', 'required'),
        );
    }
	
	public function attributeLabels()
	{
			return array(
			'BrandId'=>'Brand Name',
			'CampaignId' => 'Campaign Name',
			'ChannelId' => 'ChannelName',
			'DateFrom' => 'Date From',
			'DateTo' => 'Date To',
			'CustomerId' => 'Customer Email',
			);
	}
	
	public function save() 
	{
		Yii::app()->config->set('BrandId', $this->BrandId);
		Yii::app()->config->set('CampaignId', $this->CampaignId);
		Yii::app()->config->set('ChannelId', $this->ChannelId);
		Yii::app()->config->set('DateFrom', $this->DateFrom);
		Yii::app()->config->set('DateTo', $this->DateTo);
		Yii::app()->config->set('CustomerId', $this->CustomerId);
		return true;
    }




}
?>