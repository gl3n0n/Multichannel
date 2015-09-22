<?php

/**
 * This is the model class for table "points".
 *
 * The followings are the available columns in table 'points':
 * @property string $PointsId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $From
 * @property string $To
 * @property string $Value
 * @property string $PointAction
 * @property string $PointCapping
 * @property string $PointsLimit
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class Points extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'points';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			//array('BrandId, CampaignId, ChannelId, Value, PointAction, PointCapping, From, To, PointsLimit', 'required'),
			array('Name, BrandId, CampaignId, ChannelId', 'required'),
			array('PointsLimit, CreatedBy, UpdatedBy, BrandId, ClientId, Value', 'numerical', 'integerOnly'=>true),
			array('ClientId, BrandId, CampaignId, ChannelId', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('Status, DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PointsId, ClientId, Name, BrandId, CampaignId, ChannelId, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy,Name', 'safe', 'on'=>'search'),
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
			'pointClients'=>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'pointBrands'=>array(self::BELONGS_TO, 'Brands', 'BrandId'),
			'pointCampaigns'=>array(self::BELONGS_TO, 'Campaigns', 'CampaignId'),
			'pointChannels'=>array(self::BELONGS_TO, 'Channels', 'ChannelId'),
			'pointCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'pointUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
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
			'PointsId' => 'Points ID',
			'ClientId' => 'Client Name',
			'Name' => 'Point System Name',
			'Status' => 'Status',
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

		$criteria->compare('PointsId',$this->PointsId,true);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('CampaignId',$this->CampaignId,true);
		$criteria->compare('ChannelId',$this->ChannelId,true);
		$criteria->compare('From',$this->From,true);
		$criteria->compare('To',$this->To,true);
		$criteria->compare('Value',$this->Value,true);
		$criteria->compare('PointAction',$this->PointAction,true);
		$criteria->compare('PointCapping',$this->PointCapping,true);
		$criteria->compare('PointsLimit',$this->PointsLimit,true);
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
	 * @return Points the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
