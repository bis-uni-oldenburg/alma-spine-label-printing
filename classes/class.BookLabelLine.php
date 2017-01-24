<?php 
class BookLabelLine
{
	private $position_x = 0;
	private $position_y = 0;
	private $font_size = false;
	private $content;
	
	private $line;
	
	public function __construct()
	{
		$num_args = func_num_args();
		
		if($num_args == 1)
		{
			
		}
		else 
		{
			if($num_args >= 3)
			{
				$this->content = func_get_arg(0);
				$this->position_x = func_get_arg(1);
				$this->position_y = func_get_arg(2);
			}
			
			if($num_args == 4) $this->font_size = func_get_arg(3);
			else $this->font_size = $this->GetFontSize(2);
		}	
	}
	
	public function __toString()
	{
		return $this->Get();
	}
	
	public function Get()
	{
		$line =  sprintf("^A%s^FO%d,%d^FD%s^FS", $this->font_size, $this->position_x, $this->position_y, $this->content);
		return $line;
	}
	
	public function SetContent($content)
	{
		$this->content = $content;
	}
	
	public function SetPositionX($position_x)
	{
		$this->position_x = $position_x;
	}
	
	public function SetPositionY($position_y)
	{
		$this->position_y = $position_y;
	}
	
	public function SetFontSize($font_size)
	{
		$this->font_size = $font_size;
	}
	
	private function GetFontSize($size = 1)
	{
		if($size > 10) $size = 10;
	
		$height = 18 * $size;
		$width = 10 * $size;
	
		return "D,$height,$width";
	}
}
?>