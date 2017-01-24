<?php 

class BookLabelStorage
{
	public static function Save($book_label, $print_feedback)
	{
		$db_host = Config::Get("STORAGE_DB_HOST");
		$db_name = Config::Get("STORAGE_DB_NAME");
		$db_user = Config::Get("STORAGE_DB_USER");
		$db_password = Config::Get("STORAGE_DB_PASSWORD");
		
		$barcode = $book_label->GetBarcode();
		$zpl = $book_label->GetPrintCommand();
		
		$query = "INSERT INTO ub_booklabels (barcode, zpl, print_feedback) VALUES ('$barcode', '$zpl', '$print_feedback')";
		
		$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
		return $mysqli->query($query);
	}
	
	public static function Open($barcode)
	{
		$db_host = Config::Get("STORAGE_DB_HOST");
		$db_name = Config::Get("STORAGE_DB_NAME");
		$db_user = Config::Get("STORAGE_DB_USER");
		$db_password = Config::Get("STORAGE_DB_PASSWORD");
		
		$query = "SELECT FROM ub_booklabels WHERE barcode = '$barcode'";
	
		$mysqli = new mysqli($db_host, $db_user, $db_password, $db_name);
		return $mysqli->query($query);
	}
}
?>