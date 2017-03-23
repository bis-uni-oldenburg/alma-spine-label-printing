<?php 
class Config
{
	private static $CONFIG = null;
	private static $CONFIG_FILE_PATH = "config.ini";

	public static function Get($key)
	{
		if(self::$CONFIG == null) self::$CONFIG = parse_ini_file(self::$CONFIG_FILE_PATH);
		
		return self::$CONFIG[$key];
	}
}
?>
