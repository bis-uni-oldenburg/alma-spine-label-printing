<?php 
class BookLabelBarcode
{
	private $position_x = 0;
	private $position_y = 0;
	private $barcode;
	
	public static $BARCODE_TYPE = "B3"; // Code 39 barcode
	public static $BARCODE_WIDTH = 2;
	public static $BARCODE_HEIGHT = 116;
	public static $BARCODE_PRINT_NUMBER = "N";
	
	public function __construct()
	{
		$num_args = func_num_args();
		
		if($num_args == 3)
		{
			$this->barcode = str_pad(func_get_arg(0), 8, "0", STR_PAD_LEFT);
			$this->position_x = func_get_arg(1);
			$this->position_y = func_get_arg(2);
		}
	}
	
	public function __toString()
	{
		return $this->Get();
	}
	
	public function Get()
	{
		$line =  sprintf("^FO%d,%d^BY%d^%s,,%d,%s^FD%s^FS", 
				$this->position_x, $this->position_y, self::$BARCODE_WIDTH, 
				self::$BARCODE_TYPE, self::$BARCODE_HEIGHT, 
				self::$BARCODE_PRINT_NUMBER, $this->barcode);
	
		return $line;
	}
	
	public function SetBarcode($barcode)
	{
		$this->barcode = $barcode;
	}
	
	public function SetPositionX($position_x)
	{
		$this->position_x = $position_x;
	}
	
	public function SetPositionY($position_y)
	{
		$this->position_y = $position_y;
	}
}
?>