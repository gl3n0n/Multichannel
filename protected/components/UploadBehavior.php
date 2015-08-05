<?php

class UploadBehavior extends CActiveRecordBehavior
{
	/**
	 * name of the field that this behavior manages
	 */
	public $field = 'file';

	/**
	 * name of the path where uploaded files are saved
	 * Note: path must not end with slash ( / )
	 */
	public $path = 'assets';

	/**
	 * load file instance before validates
	 */
	public function beforeValidate($event)
	{
		$this->owner->{$this->field} = CUploadedFile::getInstance($this->owner, $this->field);
	}

	/**
	* check for file upload and save the file when uploaded
	*/
	public function beforeSave($event)
	{
		// save file to $path when $field is not null
		if ($this->owner->{$this->field} !== null)
		{
			// assign the file name
			$file = $this->owner->{$this->field}->name;
			// save $file to the $path
			$this->owner->{$this->field}->saveAs($this->path . '/' . $file);
		}
	}
}