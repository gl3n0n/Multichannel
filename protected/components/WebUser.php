<?php
class WebUser extends CWebUser
{
    const ACCESS_ADMIN            = 'ADMIN';
    const ACCESS_CAMPAIGN_MANAGER = 'CAMPAIGNMANAGER';
    const ACCESS_MANAGER          = 'MANAGER';
    const ACCESS_SUPERADMIN       = 'SUPERADMIN';

    private $_model;

    public function isSuperAdmin()
    {   return 0;
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->AccessType === self::ACCESS_SUPERADMIN;
    }

    public function isAdmin()
    {
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->AccessType === self::ACCESS_ADMIN;
    }

    public function hasRole( $role)
    {
        $user = $this->loadUser(Yii::app()->user->id);
        return $user->AccessType === $role;
    }

    public function getUserName()
    {
        $user = $this->loadUser(Yii::app()->user->id);
        return (string) $user->Username;
    }

    protected function loadUser( $id=null)
    {
        if($this->_model===null)
        {
            if($id!==null)
                $this->_model=Users::model()->findByPk($id);
        }
        return $this->_model;
    }
}
?>