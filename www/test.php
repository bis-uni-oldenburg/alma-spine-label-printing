<?php 
// Print book labels
include 'classes/autoload.php';

$DEBUG = true;


//$book_label = new BookLabel("12345678", "Just a test!", "Heuer, Lars", "nur 123.4 ein TE 5712,34a-1");
//$book_labels = BookLabel::GetLabelList(array("2424591X", "18111402"));
//$book_labels = implode("", $label_list);

$date = "2016-09-05";
$ipd = new AlmaInventoryPerDate($date);
$book_label_list = new BookLabelList($ipd->GetBarcodes());
//$book_label_list = new BookLabelList(array("2424591X", "18111402"));

echo str_replace(BookLabel::GetLineSeparator(), "<br>", $book_label_list);

$zebra_printer = "BIS_ZEBRA_Erwerbung_1006332";
$debug_printer = "edv_referat11";
$debug_printer_zi = "ZI_HP_LJ_402";

if($DEBUG) $printer = $debug_printer;
else $printer = $zebra_printer;

$printjob = new PrintJob($book_label_list, $printer);
$feedback = $printjob->Execute($DEBUG);

echo $feedback;
echo "<br><br>";
echo $printjob->GetCommand();

foreach($book_label_list->GetItems() as $label)
{
	BookLabelStorage::Save($label);
?>
<img src="<?php echo $label->GetPreviewImageSource(); ?>" width="690px" height="200px" style="display: block; margin-top: 12px; border: 1px solid #ccc" />
<br />
<?php 
}

?>