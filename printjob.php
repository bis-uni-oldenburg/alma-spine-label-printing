<?php 
// Print book labels
include 'classes/autoload.php';

$DEBUG = false;

$yesterday = date("Y-m-d", strtotime("-1 day"));

// Get newly inventoried items from Alma Analytics
$ipd = new AlmaInventoryPerDate($yesterday);

// Get List of ZPL print commands
$book_label_list = new BookLabelList($ipd->GetBarcodes());

$zebra_printer = "ZEBRA_PRINTER";
$debug_printer = "DEBUG_PRINTER";

if($DEBUG) $printer = $debug_printer;
else $printer = $zebra_printer;

// Print the labels via CUPS
$printjob = new PrintJob($book_label_list, $printer);
$feedback = $printjob->Execute();

// Save printjob to database
if(Config::Get("SAVE_PRINTJOBS"))
{
	if(!$printjob->Failed())
	{
		foreach($book_label_list->GetItems() as $label)
		{
			BookLabelStorage::Save($label, $feedback);
		}
	}
}


?>
