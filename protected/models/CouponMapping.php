<?php

/**
 * This is the model class for table "coupon_mapping".
 *
 * The followings are the available columns in table 'coupon_mapping':
 * @property string $CouponMappingId
 * @property string $CouponId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 */
class CouponMapping extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'coupon_mapping';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('CouponId, ClientId, BrandId, CampaignId, ChannelId', 'required'),
			array('CouponId, ClientId, BrandId, CampaignId, ChannelId', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CouponMappingId, CouponId, ClientId, BrandId, CampaignId, ChannelId', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CouponMappingId' => 'Coupon Mapping',
			'CouponId' => 'Coupon',
			'ClientId' => 'Client',
			'BrandId' => 'Brand',
			'CampaignId' => 'Campaign',
			'ChannelId' => 'Channel',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('CouponMappingId',$this->CouponMappingId,true);
		$criteria->compare('CouponId',$this->CouponId,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('ChannelId',$this->ChannelId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CouponMapping the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
