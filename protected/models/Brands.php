<?php

/**
 * This is the model class for table "brands".
 *
 * The followings are the available columns in table 'brands':
 * @property string $BrandId
 * @property string $ClientId
 * @property string $BrandName
 * @property string $Description
 * @property string $DurationFrom
 * @property string $DurationTo
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class Brands extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'brands';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ClientId, BrandName, Description, DurationFrom, DurationTo', 'required'),
			array('CreatedBy, UpdatedBy', 'numerical', 'integerOnly'=>true),
			array('ClientId', 'length', 'max'=>11),
			array('BrandName, Description', 'length', 'max'=>100),
			array('Status', 'length', 'max'=>8),
			array('DateCreated, DateUpdated', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('BrandId, ClientId, BrandName, Description, DurationFrom, DurationTo, Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			'brandCampaigns'=>array(self::HAS_MANY, 'Campaigns', 'BrandId'),
			'clientBrands'=>array(self::BELONGS_TO, 'Clients', 'ClientId'),
			'rewardsBrands'=>array(self::BELONGS_TO, 'RewardDetails', 'BrandId'),
			'brandCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'brandUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
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
			'BrandId' => 'Brand ID',
			'ClientId' => 'Client Name',
			'BrandName' => 'Brand Name',
			'Description' => 'Description',
			'DurationFrom' => 'Duration From',
			'DurationTo' => 'Duration To',
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

		$criteria->compare('BrandId',$this->BrandId,true);
		$criteria->compare('ClientId',$this->ClientId,true);
		$criteria->compare('BrandName',$this->BrandName,true);
		$criteria->compare('Description',$this->Description,true);
		$criteria->compare('DurationFrom',$this->DurationFrom,true);
		$criteria->compare('DurationTo',$this->DurationTo,true);
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
	 * @return Brands the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
