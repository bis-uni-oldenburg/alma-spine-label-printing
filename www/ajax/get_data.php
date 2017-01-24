<?php 
// Print book labels
include '../../classes/autoload.php';

$barcode = isset($_GET["barcode"]) ? $_GET["barcode"] : false;
if(!$barcode) exit(0);

$mode = isset($_GET["mode"]) ? $_GET["mode"] : "item";


$book_label = new BookLabel($barcode);

if($mode == "item")
{
	$data = array(
			"title" => $book_label->GetTitle(),
			"author" => $book_label->GetAuthor(),
			"call_number" => $book_label->GetCallNumber()
	);
}
else if($mode == "lines")
{
	$data = array(
			"title_line1" => $book_label->GetTitleLine(1),
			"title_line2" => $book_label->GetTitleLine(2),
			"call_number_line1" => $book_label->GetCallNumberLine(1),
			"call_number_line2" => $book_label->GetCallNumberLine(2),
			"call_number_line3" => $book_label->GetCallNumberLine(3),
			"call_number_line4" => $book_label->GetCallNumberLine(4),
			"call_number_line5" => $book_label->GetCallNumberLine(5)
	);
}

echo json_encode($data);

?>