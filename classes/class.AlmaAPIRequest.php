<?php 
class AlmaAPIRequest
{
	private static $QUERY_BASE = "/almaws/v1";
	
	private $apikey = null;
	private $query = null;
	private $method;
	private $response = null;
	
	public function __construct($query, $apikey, $method = "GET")
	{
		$this->query = $query;
		$this->method = $method;
		$this->apikey = $apikey;
		
		try 
		{
			$this->SendRequest();
		}
		catch (Exception $e)
		{
			$this->response = $e->getMessage();
		}
	}
	
	private function SendRequest()
	{
		if($this->apikey == null || $this->query == null) 
		{
			throw new Exception('API key and/or query not defined.');
			return;
		}
		
		$api_url = Config::Get("API_URL") . self::$QUERY_BASE . $this->query . "&apikey=" . $this->apikey;

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $api_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
		
		// Unicode normalization: http://php.net/manual/de/class.normalizer.php
		$this->response = Normalizer::normalize(curl_exec($ch));
		
		curl_close($ch);

	}
	
	public function GetResponse()
	{
		return $this->response;
	}
	
	public function GetResponseXMLObject()
	{
		if($this->response == null) return null;
		
		$xml_object = new SimpleXMLElement($this->response);
		return $xml_object;
	}
}
?>