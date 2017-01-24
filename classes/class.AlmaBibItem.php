<?php 
class AlmaBibItem
{
	private $barcode;
	private $item;
	private $mms_id;
	private $bib;

	private static $ITEM_QUERY_TPL = "/items?view=label&item_barcode=%s";
	private static $BIB_QUERY_TPL = "/bibs/%s/?";
	
	public function __construct($barcode)
	{
		$this->barcode = $barcode;
		
		$item_request = new AlmaAPIRequest($this->GetItemQuery(), Config::Get("BIBS_APIKEY"));
		$this->item = $item_request->GetResponseXMLObject();
		
		if($this->item != null)
		{
			$this->mms_id = $this->item->bib_data->mms_id;
			$bib_request = new AlmaAPIRequest($this->GetBibQuery(), Config::Get("BIBS_APIKEY"));
			$this->bib = $bib_request->GetResponseXMLObject();
		}
	}
	
	public function GetTitle()
	{
		if($this->bib == null) return $this->item->bib_data->title;
		else
		{
			$title = $this->bib->xpath("//datafield[@tag='245']/subfield[@code='a']");
				
			if($title) return $title[0][0];
			else return $this->item->bib_data->title;
		}
	}
	
	public function GetAuthor()
	{
		return $this->item->bib_data->author;
	}
	
	public function GetCallNumber()
	{
		return $this->item->holding_data->call_number;
	}
	
	public function GetAlternativeCallNumber()
	{
		return $this->item->item_data->alternative_call_number;
	}
	
	public function GetAlmaParsedCallNumber()
	{
		return $this->item->item_data->parsed_call_number->call_no;
	}
	
	public function GetAlmaParsedAlternativeCallNumber()
	{
		return $this->item->item_data->parsed_alt_call_number->alt_call_no;
	}
	
	
	private function GetItemQuery()
	{
		$query = sprintf(self::$ITEM_QUERY_TPL, $this->barcode);
		return $query;
	}
	
	private function GetBibQuery()
	{
		$query = sprintf(self::$BIB_QUERY_TPL, $this->mms_id);
		return $query;
	}
}
?>