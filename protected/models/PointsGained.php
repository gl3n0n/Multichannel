<?php

/**
 * This is the model class for table "rewards_list".
 *
 * The followings are the available columns in table 'maps_list':
 * @property string $RewardId
 * @property string $Title
 * @property string $Description
 * @property string $Image
 * @property string $Availability
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class PointsGained extends CActiveRecord
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
			);
	}

	public function scopes()
	{
		return array(
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
			'mapCreateUsers'=>array(self::BELONGS_TO, 'Users',       'CreatedBy'),
			'mapUpdateUsers'=>array(self::BELONGS_TO, 'Users',       'UpdatedBy'),
			'mapCustomers'  =>array(self::BELONGS_TO, 'Customers',   'CustomerId'),
			'mapClients'    =>array(self::BELONGS_TO, 'Clients',     'ClientId'),
			'mapBrands'     =>array(self::BELONGS_TO, 'Brands',      'BrandId'),
			'mapCampaigns'  =>array(self::BELONGS_TO, 'Campaigns',   'CampaignId'),
			'mapChannels'   =>array(self::BELONGS_TO, 'Channels',    'ChannelId'),
			'mapPoints'     =>array(self::BELONGS_TO, 'CustomerPoints', 'SubscriptionId'),
			'mapBalance'    =>array(self::STAT,       'CustomerPoints', 'Balance'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'SubscriptionId'  => 'SubscriptionId',
			'DateCreated' => 'Date Created',
			'CreatedBy'   => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy'   => 'Updated By',
			'CustomerId'  => 'Customer',
			'ClientId'  => 'Client Name',
			'BrandId'   => 'Brand Name',
			'ChannelId' => 'Channel Name',
			'ClientId'  => 'Client Name',
			'CampaignId'=> 'Campaign Name',
			'Balance'   => 'Points',
			
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

		$criteria->compare('RewardId',$this->RewardId,true);
		$criteria->compare('Status',$this->Status,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return RewardsList the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
