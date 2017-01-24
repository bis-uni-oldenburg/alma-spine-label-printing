<html>
<head>
<title>Print spine label</title>
<style type="text/css">
body {
	font-family: Calibri, Arial, sans-serif;
}

#menu {
	float: right;
	margin: 5px 10px 5px 10px;
}

.form {

	font-size: 13px;

}

.form table {
	float: left;
	margin-right: 12px;

	width: 500px;
}

.form table tr.headline td {
	padding-bottom: 14px;
}

.form td {
	padding-right: 8px;
}

.form input {
	width: 250px;
}

.form button[type='submit'] {
	margin-top: 14px;
}

.zpl {
	margin-left: 630px;
	margin-top: -36px;
}

.print-command {
	margin-top: 24px;
}

.preview {
	margin-top: 16px;
}

.monospace {
	font-family: monospace; 
	font-size: 13px;
}

div.monospace {
	margin-top: 8px;
}

.print {
	margin-top: 16px;
}
</style>
<script src="../jquery/jquery.js"></script>
<script language="javascript">
function Print()
{
	document.label_data.print.value = 1;
	document.label_data.submit();
}

function GetItemByBarcode()
{
	var barcode = $("#barcode").val();
	var url = "ajax/get_data.php";
	console.log(barcode);
	$.ajax({
		  url: url,
		  type: "GET",
		  data: 
		  {
			  barcode: barcode,
			  mode: "item"
		  },
		  success: function(response) 
		  {
			  console.log(response);
			  var data = $.parseJSON(response);
			  
			  $("input[name='title']").val(data["title"]);
			  $("input[name='author']").val(data["author"]);
			  $("input[name='call_number']").val(data["call_number"]);
		  }
	});
}

function GetLinesByBarcode()
{
	var barcode = $("#barcode").val();
	var url = "ajax/get_data.php";
	console.log(barcode);
	$.ajax({
		  url: url,
		  type: "GET",
		  data: 
		  {
			  barcode: barcode,
			  mode: "lines"
		  },
		  success: function(response) 
		  {
			  console.log(response);
			  var data = $.parseJSON(response);
			  
			  $("input[name='title_line1']").val(data["title_line1"]);
			  $("input[name='title_line2']").val(data["title_line2"]);
			  $("input[name='barcode_line']").val(data["barcode_line"]);
			  $("input[name='call_number_line1']").val(data["call_number_line1"]);
			  $("input[name='call_number_line2']").val(data["call_number_line2"]);
			  $("input[name='call_number_line3']").val(data["call_number_line3"]);
			  $("input[name='call_number_line4']").val(data["call_number_line4"]);
			  $("input[name='call_number_line5']").val(data["call_number_line5"]);
			  $("input[name='barcode_line']").val(barcode);
		  }
	});
}
</script>
</head>
<body>

<?php 
// Print book labels
include '../classes/autoload.php';

if(!isset($_GET["mode"])) $mode = 1;
else $mode = $_GET["mode"];

$valid = false;

if($mode == 1)
{
	
?>
	<div id="menu">
	<strong><a href="custom.php?mode=1">Print spine label (1)</a></strong> | <a href="custom.php?mode=2">Print spine label (2)</a>
	</div>
	<h1>Print spine label (1)</h1>
<?php
	if(!isset($_GET["barcode"])) $barcode = $title = $author = $call_number = $print = "";
	else
	{
		$barcode = $_GET["barcode"];
		$title = $_GET["title"];
		$author = $_GET["author"];
		$call_number = $_GET["call_number"];
		$print = $_GET["print"];
		
		if($barcode and $title and $call_number)
		{
			$book_label = new BookLabel($barcode, $title, $author, $call_number);
			$valid = true;
		}

	}	
	?>
	<form class="form" name="label_data" method="GET" action="print_custom_label.php">
	<table>
	<tr><td>Barcode</td><td><input name="barcode" id="barcode" type="text" value="<?php echo $barcode; ?>" /></td><td><button type="button" onclick="GetItemByBarcode()">Daten ermitteln</button></td></tr>
	<tr><td>Title</td><td><input name="title" type="text" value="<?php echo $title; ?>" /></td><td>&nbsp;</td></tr>
	<tr><td>Author</td><td><input name="author" type="text" value="<?php echo $author; ?>" /></td><td>&nbsp;</td></tr>
	<tr><td>Call number</td><td><input name="call_number" type="text" value="<?php echo $call_number; ?>" /></td><td>&nbsp;</td></tr>
	<tr><td><button type="submit">Preview</button></td>&nbsp;<td></td><td>&nbsp;</td></tr>
	</table>
	<input name="print" type="hidden" value="0" />
	<input name="mode" type="hidden" value="<?php echo $mode; ?>" />
	

<?php
}
else if($mode == 2)
{
?>
	<div id="menu">
	<a href="custom.php?mode=1">Print spine label (1)</a> | <strong><a href="custom.php?mode=2">Print spine label (2)</a></strong>
	</div>
	<h1>Print spine label (2)</h1>
<?php
	if(!isset($_GET["barcode"]))
	{
		$barcode = $barcode_line = $title_line1 = $title_line2 = $call_number_line1 = $call_number_line2 = $call_number_line3 = $call_number_line4 = $call_number_line5 = $print = "";
	}
	else
	{
		$barcode = $_GET["barcode"];
		$barcode_line = $_GET["barcode_line"];
		$title_line1 = $_GET["title_line1"];
		$title_line2 = $_GET["title_line2"];
		$call_number_line1 = $_GET["call_number_line1"];
		$call_number_line2 = $_GET["call_number_line2"];
		$call_number_line3 = $_GET["call_number_line3"];
		$call_number_line4 = $_GET["call_number_line4"];
		$call_number_line5 = $_GET["call_number_line5"];
		$print = $_GET["print"];
	
		if($barcode and $title_line1 and $call_number_line1)
		{
			$book_label = new BookLabel($barcode_line, $title_line1, $title_line2, $call_number_line1, $call_number_line2, $call_number_line3, $call_number_line4, $call_number_line5);;
			$valid =true;
		}
	}
?>
	<form class="form" name="label_data" method="GET" action="custom.php">
	<table>
	<tr class="headline"><td><input name="barcode" id="barcode" placeholder="Barcode" type="text" value="<?php echo $barcode; ?>" /></td><td><button type="button" onclick="GetLinesByBarcode()">Daten ermitteln</button></td></tr>
	
	<tr><td><input name="title_line1" type="text" placeholder="Author/Title, Row 1" value="<?php echo $title_line1; ?>" /></td><td><input name="call_number_line1" placeholder="Signatur, Zeile 1" type="text" value="<?php echo $call_number_line1; ?>" /></td></tr>
	<tr><td><input name="title_line2" type="text" placeholder="Author/Title, Row" value="<?php echo $title_line2; ?>" /></td><td><input name="call_number_line2" placeholder="Signatur, Zeile 2" type="text" value="<?php echo $call_number_line2; ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><input name="call_number_line3" placeholder="Call number, Row 3" type="text" value="<?php echo $call_number_line3; ?>" /></td></tr>
	<tr><td>&nbsp;</td><td><input name="call_number_line4" placeholder="Call number, Row 4" type="text" value="<?php echo $call_number_line4; ?>" /></td></tr>
	<tr><td><input name="barcode_line" placeholder="Barcode" type="text" value="<?php echo $barcode; ?>" /></td><td><input name="call_number_line5" placeholder="Signatur, Zeile 5" type="text" value="<?php echo $call_number_line5; ?>" /></td></tr>
	<tr><td><button type="submit">Vorschau</button></td>&nbsp;<td></td></tr>
	</table>
	<input name="print" type="hidden" value="0" />
	<input name="mode" type="hidden" value="2" />
	
<?php
}

if($valid)
{
?>
<div class="zpl">
	<strong>ZPL-Druckjob:</strong>
	<div class="monospace">
<?php
	echo str_replace(BookLabel::GetLineSeparator(), "<br>", $book_label);
?>
	</div>
</div>
<?php
	
	if($print)
	{
		$printer = $_GET["printer"];
		//$printer = $debug_printer;
		
		if($printer)
		{
			$printjob = new PrintJob($book_label, $printer, "-" . $barcode);
			if($print) $printjob->Execute();

	

		
?>
<div class="print-command"><strong>Druck-Befehl:</strong> <span class="monospace"><?php echo $printjob->GetCommand(); ?></span></div>
<?php 
		}
	}
?>
<div class="preview">
<Strong>Vorschau:</Strong><br />
<img src="<?php echo $book_label->GetPreviewImageSource(); ?>" width="690px" height="200px" style="display: block; margin-top: 12px; border: 1px solid #ccc" />
</div>
<div class="print">
Drucker: 
<select name="printer">
	<option selected value="0">[Bitte auswählen]</option>
	<option value="BIS_ZEBRA_Erwerbung_1006332">Zebra-Drucker, Erwerbung (1006332)</option>
	<option value="edv_referat1">HP Laserjet, EDV (Test)</option>
	<option value="ZI_HP_LJ_402">HP Laserjet, ZI</option>
</select>
<button onclick="Print()">Drucken</button>
</form>
<?php 
	
}
?>
</body>
</html>