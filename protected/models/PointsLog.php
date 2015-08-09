<?php

/**
 * This is the model class for table "points_log".
 *
 * The followings are the available columns in table 'points_log':
 * @property string $PointLogId
 * @property string $CustomerId
 * @property integer $SubscriptionId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $PointsId
 * @property string $DateCreated
 * @property integer $CreatedBy
 */
class PointsLog extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'points_log';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('SubscriptionId', 'required'),
			array('SubscriptionId, CreatedBy', 'numerical', 'integerOnly'=>true),
			array('DateCreated', 'safe'),
			array('CustomerId, ClientId, BrandId, CampaignId, ChannelId', 'length', 'max'=>11),
			array('Points', 'match', 'pattern'=>'/^(-)?[0-9]+$/'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PointLogId, CustomerId, SubscriptionId, ClientId, BrandId, CampaignId, ChannelId, DateCreated, CreatedBy', 'safe', 'on'=>'search'),
			//array('PointLogId, CustomerId, SubscriptionId, ClientId, BrandId, CampaignId, ChannelId, DateCreated, CreatedBy', 'safe', 'on'=>'search'),
			array('BrandId, CampaignId, ChannelId, DateCreated', 'safe', 'on'=>'searchi'),
			// array('BrandId, CampaignId, ChannelId, DateCreated', 'safe', 'on'=>'searchi'),
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
			'pointlogClients'=>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'pointlogBrands'=>array(self::BELONGS_TO, 'Brands', 'BrandId'),
			'pointlogCampaigns'=>array(self::BELONGS_TO, 'Campaigns', 'CampaignId'),
			'pointlogChannels'=>array(self::BELONGS_TO, 'Channels', 'ChannelId'),
			'pointlogCustomers'=>array(self::BELONGS_TO, 'Customers', 'CustomerId'),
			'pointlogPoints'=>array(self::BELONGS_TO, 'Points', 'PointsId'),
			'pointlogCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'pointlogUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),

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
			'PointLogId' => 'Point Log ID',
			'CustomerId' => 'Customer Name',
			'SubscriptionId' => 'Subscription',
			'ClientId' => 'Client Name',
			'BrandId' => 'Brand Name',
			'CampaignId' => 'Campaign Name',
			'ChannelId' => 'Channel Name',
			'Points'    => 'Points Value',
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

		$criteria->compare('PointLogId',$this->PointLogId,true);
		$criteria->compare('CustomerId',$this->CustomerId,true);
		$criteria->compare('SubscriptionId',$this->SubscriptionId);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('ChannelId',$this->ChannelId,true);
		$criteria->compare('PointsId',$this->PointsId,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PointsLog the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
