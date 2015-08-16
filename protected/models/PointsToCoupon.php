<?php

/**
 * This is the model class for table "points".
 *
 * The followings are the available columns in table 'points':
 * @property string $PointsToCouponId
 * @property string $ClientId
 * @property string $BrandId
 * @property string $CampaignId
 * @property string $ChannelId
 * @property string $From
 * @property string $To
 * @property string $Value
 * @property string $PointAction
 * @property string $PointCapping
 * @property string $PointsToCouponLimit
 * @property string $Status
 * @property string $DateCreated
 * @property integer $CreatedBy
 * @property string $DateUpdated
 * @property integer $UpdatedBy
 */
class PointsToCoupon extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'points_to_coupon';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('Title, CouponId,PointsRequired,CouponValue', 'required'),
			array('CouponId, PointsRequired, CouponValue', 'numerical', 'integerOnly'=>true),
			array('Status', 'length', 'max'=>8),
			
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('PtcId,CouponId,PointsRequired,CouponValue,Title,Status, DateCreated, CreatedBy, DateUpdated, UpdatedBy', 'safe', 'on'=>'search'),
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
			'p2couponCoupon'     =>array(self::BELONGS_TO, 'Coupon', 'CouponId'),
			'p2couponMap'        =>array(self::HAS_MANY,   'CouponMapping','CouponId'),
			'p2couponCreateUsers'=>array(self::BELONGS_TO, 'Users',  'CreatedBy'),
			'p2couponUpdateUsers'=>array(self::BELONGS_TO, 'Users',  'UpdatedBy'),
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
			'PtcId'       => 'Seq #',
			'CouponId'    => 'Coupon Id',
			'PointsRequired' => 'Points Required',
			'CouponValue'    => 'Coupon Value',
			'Title'       => 'Title',
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

		$criteria->compare('PtcId',      $this->PtcId,true);
		$criteria->compare('CouponId',   $this->CouponId,true);
		$criteria->compare('PointsRequired',    $this->PointsRequired,true);
		$criteria->compare('CouponValue', $this->CouponValue,true);
		$criteria->compare('Title',      $this->Title,true);
		$criteria->compare('Status',     $this->Status,true);
		$criteria->compare('DateCreated',$this->DateCreated,true);
		$criteria->compare('CreatedBy',  $this->CreatedBy);
		$criteria->compare('DateUpdated',$this->DateUpdated,true);
		$criteria->compare('UpdatedBy',  $this->UpdatedBy);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return PointsToCoupon the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
