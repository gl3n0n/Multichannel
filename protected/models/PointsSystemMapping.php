<?php

/**
 * This is the model class for table "points_mapping".
 *
 * The followings are the available columns in table 'points_mapping':
 * @property string $PointMappingId
 * @property string $PointsId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 */
class PointsSystemMapping extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'points_mapping';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('PointsId, ClientId, BrandId, CampaignId, ChannelId', 'required'),
			array('PointsId, ClientId, BrandId, CampaignId, ChannelId', 'length', 'max'=>11),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PointMappingId, PointsId, ClientId, BrandId, CampaignId, ChannelId', 'safe', 'on'=>'search'),
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
			'byClients'      =>array(self::BELONGS_TO, 'Clients',      'ClientId'),
			'byBrands'       =>array(self::BELONGS_TO, 'Brands',       'BrandId'),
			'byCampaigns'    =>array(self::BELONGS_TO, 'Campaigns',    'CampaignId'),
			'byChannels'     =>array(self::BELONGS_TO, 'Channels',     'ChannelId'),
			'byCreateUsers'  =>array(self::BELONGS_TO, 'Users',        'CreatedBy'),
			'byUpdateUsers'  =>array(self::BELONGS_TO, 'Users',        'UpdatedBy'),
			'byPointsSystem' =>array(self::BELONGS_TO, 'PointsSystem', 'PointsId'),
			'byActionType'   =>array(self::BELONGS_TO, 'ActionType',   'PointsId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'PointMappingId' => 'ID',
			'PointsId' => 'Points System',
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

		$criteria->compare('PointMappingId',$this->PointMappingId,true);
		$criteria->compare('PointsId',$this->PointsId,true);
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
	 * @return PointsSystemMapping the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
