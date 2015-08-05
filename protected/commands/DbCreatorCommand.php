<?php
class DbCreatorCommand extends CConsoleCommand
{
	// console commands = os-specific
	public function run($args)
	{
		// do stuff here
		echo 'foo' . PHP_EOL;
		echo `ipconfig /all`; // this is a backtick ` not a single quote '
	}
}
?>