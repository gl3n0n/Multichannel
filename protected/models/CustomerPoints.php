<?php

/**
 * This is the model class for table "customer_points".
 *
 * The followings are the available columns in table 'customer_points':
 * @property string $CustomerPointId
 * @property integer $SubscriptionId
 * @property string $Balance
 * @property string $Used
 * @property string $Total
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class CustomerPoints extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'customer_points';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SubscriptionId, Balance, Used, Total', 'required'),
			array('SubscriptionId, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('Balance, Used, Total', 'length', 'max'=>11),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('CustomerPointId, SubscriptionId, Balance, Used, Total, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			'subsCustSubs'       =>array(self::MANY_MANY,  'CustomerSubscriptions', 'SubscriptionId'),
			/**
			'subsCustClients'    =>array(self::MANY_MANY,  'Clients', 
						'Customer_Subscriptions(SubscriptionId,ClientId)'),
			'subsCustBrands'     =>array(self::MANY_MANY,  'Brands', 
						'Customer_Subscriptions(SubscriptionId,BrandId)'), 
			'subsCustChannels'   =>array(self::MANY_MANY,  'Channels', 
						'Customer_Subscriptions(SubscriptionId,ChannelId)'),                				                				
			'subsCustCampaigns'  =>array(self::MANY_MANY,  'Campaigns', 
						'Customer_Subscriptions(SubscriptionId,CampaignId)'), **/      			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'CustomerPointId' => 'Customer Point',
			'SubscriptionId' => 'Subscription',
			'Balance' => 'Balance',
			'Used' => 'Used',
			'Total' => 'Total',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
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

		$criteria->compare('CustomerPointId',$this->CustomerPointId,true);
		$criteria->compare('SubscriptionId',$this->SubscriptionId);
		$criteria->compare('Balance',$this->Balance,true);
		$criteria->compare('Used',$this->Used,true);
		$criteria->compare('Total',$this->Total,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return CustomerPoints the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
