<?php

/**
 * This is the model class for table "raffle".
 *
 * The followings are the available columns in table 'raffle':
 * @property string $RaffleId
 * @property string $RaffleName
 * @property string $Source
 * @property integer $NoOfWinners
 * @property integer $BackUp
 * @property string $FdaNo
 * @property string $DrawDate
 * @property string $DateCreated
 * @property string $CreatedBy
 * @property string $DateUpdated
 * @property string $UpdatedBy
 * @property string $Status
 * @property string $CouponId
 */
class Raffle extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'raffle';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('RaffleName, Source, FdaNo, CouponId', 'required'),
			array('NoOfWinners, BackUp', 'numerical', 'integerOnly'=>true),
			array('Source, FdaNo', 'length', 'max'=>50),
			array('CreatedBy, UpdatedBy', 'length', 'max'=>30),
			array('CouponId', 'length', 'max'=>11),
			array('Status', 'length', 'max'=>8),
			array('DrawDate, DateCreated, DateUpdated', 'safe'),
			array('CouponId', 'default', 'setOnEmpty' => true, 'value' => NULL),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('RaffleId, ClientId, RaffleName, Source, NoOfWinners, BackUp, FdaNo, DrawDate, DateCreated, CreatedBy, DateUpdated, UpdatedBy, Status, CouponId', 'safe', 'on'=>'search'),
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
			'raffleClients'    =>array(self::BELONGS_TO, 'Clients','ClientId'),
			'raffleCoupon'     =>array(self::BELONGS_TO, 'Coupon', 'CouponId'),
			'raffleCreateUsers'=>array(self::BELONGS_TO, 'Users', 'CreatedBy'),
			'raffleUpdateUsers'=>array(self::BELONGS_TO, 'Users', 'UpdatedBy'),
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
			'RaffleId' => 'Raffle',
			'RaffleName' => 'Raffle Name',
			'Source' => 'Source',
			'ClientId' => 'Client Name',
			'NoOfWinners' => 'No Of Winners',
			'BackUp' => 'BackUp',
			'FdaNo' => 'Promo Permit No',
			'DrawDate' => 'Draw Date',
			'DateCreated' => 'Date Created',
			'CreatedBy' => 'Created By',
			'DateUpdated' => 'Date Updated',
			'UpdatedBy' => 'Updated By',
			'Status' => 'Status',
			'CouponId' => 'Coupon',
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

		$criteria->compare('RaffleId',$this->RaffleId,true);
		$criteria->compare('RaffleName',$this->RaffleName,true);
		$criteria->compare('Source',$this->Source,true);
		$criteria->compare('NoOfWinners',$this->NoOfWinners);
		$criteria->compare('BackUp',$this->BackUp);
		$criteria->compare('FdaNo',$this->FdaNo,true);
		$criteria->compare('DrawDate',$this->DrawDate,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',$this->CreatedBy,true);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',$this->UpdatedBy,true);
		$criteria->compare('Status',$this->Status,true);
		$criteria->compare('CouponId',$this->CouponId,true);
		$criteria->compare('ClientId',$this->ClientId,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Raffle the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
