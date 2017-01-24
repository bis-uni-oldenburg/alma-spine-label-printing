<?php 
class AlmaInventoryPerDate
{
	private $apikey;
	private $analytics_path;
	
	private $items = null;
	private $barcodes = null;

	private $date1;
	private $date2;
	
	private static $ANALYTICS_QUERY_TPL = "/analytics/reports?path=%s&filter=%s";
	private static $ANALYTICS_QUERY_TOKEN_TPL = "/analytics/reports?token=%s";
	
	private static $FILTER_TPL =
	"<sawx:expr xsi:type=\"sawx:comparison\" op=\"between\" xmlns:saw=\"com.siebel.analytics.web/report/v1.1\"
		   		xmlns:sawx=\"com.siebel.analytics.web/expression/v1.1\"
		   		xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
		   		xmlns:xsd=\"http://www.w3.org/2001/XMLSchema\">
	 		<sawx:expr xsi:type=\"sawx:sqlExpression\">\"Physical Item Details\".\"Inventory Date\"</sawx:expr>
			<sawx:expr xsi:type=\"xsd:dateTime\">%sT00:00:00</sawx:expr>
            <sawx:expr xsi:type=\"xsd:dateTime\">%sT00:00:00</sawx:expr>
	 </sawx:expr>";

	
	public function __construct($date1, $date2 = false)
	{
		$this->apikey = Config::Get("ANALYTICS_APIKEY");
		$this->analytics_path = Config::Get("ANALYTICS_PATH");
		
		$this->items = array();
		$this->barcodes = array();
		
		if(!$date2) 
		{
			$date2 = $date1;
			//$date2 = date("Y-m-d", strtotime("$date1 +1 day"));
		}
		
		$this->date1 = $date1;
		$this->date2 = $date2;

		//$request = new AlmaAPIRequest($this->GetAnalyticsQuery(), self::$APIKEY);
		
		$request = new AlmaAPIRequest($this->GetAnalyticsQuery(), $this->apikey);
		
		$report = $request->GetResponseXMLObject();
		$is_finished = $report->QueryResult->IsFinished;
		$resumption_token = $report->QueryResult->ResumptionToken;
		$rowset = $report->QueryResult->ResultXml->rowset;
		
		self::AddItems($rowset);
		
		while($is_finished == "false")
		{
			$request = new AlmaAPIRequest($this->GetAnalyticsQuery($resumption_token), self::$APIKEY);
			$report = $request->GetResponseXMLObject();
			$rowset = $report->QueryResult->ResultXml->rowset;
			$is_finished = $report->QueryResult->IsFinished;
			
			self::AddItems($rowset);
		}
	
	}
	
	public function Get()
	{
		return $this->items;
	}
	
	public function GetBarcodes()
	{
		return $this->barcodes;
	}
	
	private function AddItems($rowset)
	{
		foreach($rowset->Row as $row)
		{
			$item = array();
			$item["barcode"] = (string) $row->Column6;
			$item["inventory_date"] = (string) $row->Column8;
			$item["call_number"] = (string) $row->Column5;
			$item["alt_call_number"] = (string) $row->Column7;
			$item["author"] = isset($row->Column1) ? (string) $row->Column1 : "";
			$item["title"] = (string) $row->Column3;
			$item["holding_id"] = (string) $row->Column4;
			$item["item_id"] = (string) $row->Column9;
			$item["bib_id"] = isset($row->Column2) ? (string) $row->Column2 : 0;
			
			$this->barcodes[] = $item["barcode"];
			$this->items[] = $item;
		}
		
	}
	
	private function GetFilter()
	{
		$filter = urlencode(sprintf(self::$FILTER_TPL, $this->date1, $this->date2));
		return $filter;
	}
	
	private function GetPath()
	{
		return $this->analytics_path;
	}
	
	private function GetAnalyticsQuery($resumption_token = false)
	{
		if($resumption_token)
		{
			$query = sprintf(self::$ANALYTICS_QUERY_TOKEN_TPL, $resumption_token);
		}
		else $query = sprintf(self::$ANALYTICS_QUERY_TPL, $this->GetPath(), $this->GetFilter());
		
		return $query;
	}
	
}
?>