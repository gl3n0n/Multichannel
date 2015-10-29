<?php

/**
 * This is the model class for table "customer_subscriptions".
 *
 * The followings are the available columns in table 'customer_subscriptions':
 * @property string $SubscriptionId
 * @property string $CustomerId
 * @property integer $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class CustomerSubscriptions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'customer_subscriptions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ClientId, CustomerId, BrandId, CampaignId,PointsId', 'required'),
			array('ClientId, CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('CustomerId, BrandId, CampaignId,PointsId', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('SubscriptionId, CustomerId, ClientId, BrandId, CampaignId, PointsId, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			'subsClients'=>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'subsBrands'=>array(self::BELONGS_TO, 'Brands', 'BrandId'),
			'subsCampaigns'=>array(self::BELONGS_TO, 'Campaigns', 'CampaignId'),
			'subsChannels'=>array(self::BELONGS_TO, 'Channels', 'ChannelId'),
			'subsCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'subsUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
			'subsCustomers'=>array(self::BELONGS_TO, 'Customers', 'CustomerId'),
			'subsCustPoints'=>array(self::BELONGS_TO, 'CustomerPoints', 'SubscriptionId'),
			'subsPoints'=>array(self::BELONGS_TO, 'PointsSystem', 'PointsId'),
		);
	}
	
	public function scopes()
	{
		return array(
			'thisClient'=>array(
				'condition'=>'ClientId = :modelClientId',
				'params' => array(':modelClientId'=>Yii::app()->utils->getUserInfo('ClientId')),
			),
			'active'=>array(
				'condition'=>"Status='ACTIVE'",
			),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'SubscriptionId' => 'Subscription',
			'CustomerId'  => 'Customer',
			'ClientId'    => 'Client',
			'BrandId'     => 'Brand',
			'CampaignId'  => 'Campaign',
			'PointsId'    => 'Action Type',
			'PointsValue' => 'Value',
			'Status'      => 'Status',
			'DateCreated' => 'Date Created',
			'CreatedBy'   => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy'   => 'Updated By',
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

		$criteria->compare('SubscriptionId',$this->SubscriptionId,true);
		$criteria->compare('CustomerId',$this->CustomerId,true);
		$criteria->compare('ClientId',$this->ClientId);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('PointsId',$this->PointsId,true);
		$criteria->compare('Status',$this->Status,true);
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
	 * @return CustomerSubscriptions the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
