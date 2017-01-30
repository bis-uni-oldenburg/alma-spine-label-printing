<?php 
// Print spine labels 
include dirname( dirname(__FILE__) ) . '/classes/autoload.php'; 

$DEBUG = true;

// Take date from command line if supplied
if(isset($argv[1])) 
{	
	$date = $argv[1];
}
// ... otherwise take yesterday's date
else $date = date("Y-m-d", strtotime("-1 day"));

// Get newly inventoried items from Alma Analytics
$ipd = new AlmaInventoryPerDate($date);

if(!count($ipd->GetBarcodes()))
{
	echo "No items available for $date.\n";
	exit;
}

// Get List of ZPL print commands
$book_label_list = new BookLabelList($ipd->GetBarcodes());

// Get available printers
if(!$DEBUG) $printers = Config::Get("PRINTERS");
else $printers = Config::Get("DEBUG_PRINTERS");

$printer = $printers[0];

// Print the labels via CUPS
$printjob = new PrintJob($book_label_list, $printer, $date);
$feedback = $printjob->Execute();

echo $feedback . "\n";
?>
