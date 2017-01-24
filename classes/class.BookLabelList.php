<?php 
class BookLabelList
{
	private $barcodes;
	private $list = null;
	
	public function __construct($barcodes)
	{
		if(!is_array($barcodes)) $barcodes = array($barcodes);
		$this->barcodes =$barcodes;
			
		$this->list = array();
			
		foreach($this->barcodes as $barcode)
		{
			$this->list[] = new BookLabel($barcode);
		}
	}
	
	public function GetItems()
	{
		return $this->list;
	}
	
	public function __toString()
	{
		return implode("", $this->list);
	}
}
?>