<?php 
// Print book labels
include 'classes/autoload.php';

$DEBUG = true;

if($DEBUG) $yesterday = date("Y-m-d", strtotime("-1 day"));
else $yesterday = date("Y-m-d", strtotime("2016-09-02 -1 day"));

$ipd = new AlmaInventoryPerDate($yesterday);

$book_label_list = new BookLabelList($ipd->GetBarcodes());

$zebra_printer = "ZEBRA_PRINTER";
$debug_printer = "DEBUG_PRINTER";

if($DEBUG) $printer = $debug_printer;
else $printer = $zebra_printer;

$printjob = new PrintJob($book_label_list, $printer);
$feedback = $printjob->Execute();

if(!$printjob->Failed())
{
	foreach($book_label_list->GetItems() as $label)
	{
		BookLabelStorage::Save($label, $feedback);
	}
}

?>