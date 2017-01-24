<?php 
// Print book labels
include '../classes/autoload.php';

$DEBUG = true;

//$yesterday = date("Y-m-d", strtotime("-1 day"));
$yesterday = date("Y-m-d", strtotime("2016-09-02 -1 day"));

$ipd = new AlmaInventoryPerDate($yesterday);

//var_dump($ipd->GetBarcodes());
//exit;

$book_label_list = new BookLabelList($ipd->GetBarcodes());

$zebra_printer = "BIS_ZEBRA_Erwerbung_1006332";
$debug_printer = "edv_referat11";

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


// CUPS feedback:
// lp: The printer or class does not exist.
// lp: Error - unable to access "/var/www/html/booklabel/labels_zpl/labels_2017-01-10b.jobx" - Datei oder Verzeichnis nicht gefunden
// request id is edv_referat1-133 (1 file(s))
?>