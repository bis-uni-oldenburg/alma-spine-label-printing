<?php 

class BookLabel
{
	private $item_barcode;
	private $bib_item = null;
	private $label_lines = array();
	private $print_command = null;
	
	private $title;
	private $author;
	private $call_number;
	
	private $title_line1;
	private $title_line2;
	
	private $call_number_line1;
	private $call_number_line2;
	private $call_number_line3;
	private $call_number_line4;
	private $call_number_line5;
	
	private static $LABEL_LIST = null;

	private static $LINE_SEPARATOR = "\n"; // or: \r\n
	private static $MAX_TITLE_LINE_LENGTH = 30;
	private static $ENCODING = "28"; // 28: UTF-8
	
	private static $LIBRARY = "UB Oldenburg";
	
	// ger 198.2 q CK 1800,24f-1
	private static $CALL_NUMBER_REGEX = "!([a-z]{3}) ([0-9\.]+)( .+)? ([A-Z]{2} [0-9\,]+)([a-z\-0-9]+)?$!";

	// ZPL Programming Guide: http://www.tracerplus.com/kb/Manuals/ZPL_Vol1.pdf
	
	public function __construct()
	{
		$num_args = func_num_args();
		
		if($num_args == 1)
		{
			$this->item_barcode = func_get_arg(0);
			$this->bib_item = new AlmaBibItem($this->item_barcode);
			
			$this->title = (string) $this->bib_item->GetTitle();
			$this->author = (string) $this->bib_item->GetAuthor();
			$this->call_number = (string) $this->bib_item->GetAlternativeCallNumber();
			
			$this->SetTitleLines();
			$this->SetCallNumberLines();
		}
		else if($num_args == 4)
		{
			$this->item_barcode = func_get_arg(0);
			$this->title = func_get_arg(1);
			$this->author = func_get_arg(2);
			$this->call_number = func_get_arg(3);
			
			$this->SetTitleLines();
			$this->SetCallNumberLines();
		}
		else if($num_args == 8)
		{
			$this->item_barcode = func_get_arg(0);
			$this->title_line1 = func_get_arg(1);
			$this->title_line2 = func_get_arg(2);
			$this->call_number_line1 = func_get_arg(3);
			$this->call_number_line2 = func_get_arg(4);
			$this->call_number_line3 = func_get_arg(5);
			$this->call_number_line4 = func_get_arg(6);
			$this->call_number_line5 = func_get_arg(7);
		}

		$this->label_lines = null;
	}
	
	public static function GetLabelList($barcodes, $as_string = true)
	{
		if(self::$LABEL_LIST == null)
		{
			if(!is_array($barcodes)) $barcodes = array($barcodes);
			
			self::$LABEL_LIST = array();
			
			foreach($barcodes as $barcode)
			{
				self::$LABEL_LIST[] = new BookLabel($barcode);
			}
		}

		if($as_string) return implode("", self::$LABEL_LIST);
		else return self::$LABEL_LIST;
	}
	
	public function GetTitle() { return $this->title; }
	
	public function GetAuthor() { return $this->author; }
	
	public function GetCallNumber() { return $this->call_number; }
	
	public function GetBarcode() { return $this->item_barcode; }
	
	public function GetTitleLine($number) 
	{ 
		$line = "title_line$number";
		return $this->$line; 
	}
	
	public function GetCallNumberLine($number)
	{
		$line = "call_number_line$number";
		return $this->$line;
	}
	
	private function Add($book_label_line)
	{
		$this->label_lines[] = $book_label_line;
	}
	
	private function Begin()
	{
		$this->Add("^XA");
	}
	
	private function End()
	{
		$this->Add("^XZ");
	}
	
	private function SetTitleLines()
	{
		if($this->author) $author = $this->author . ": ";
		else $author = "";
		$title = $this->title;
		$total = $author . $title;
		$max_line = self::$MAX_TITLE_LINE_LENGTH;
		$max_total = $max_line * 2;
		
		$title_line1 = "";
		$title_line2 = "";
		
		$author_length = strlen($author);
		$title_length = strlen($title);
		$total_length = $author_length + $title_length;
		
		if(!$author_length)
		{
			$lines = self::SpreadEvenly($title, 2, $max_line);
			$title_line1 = $lines[0];
			$title_line2 = $lines[1];
		}
		else if($total_length <= $max_line)
		{
			$title_line1 = $total;
			$title_line2 = "";
		}
		else if($author_length <= $max_line and $title_length <= $max_line)
		{
			$title_line1 = $author;
			$title_line2 = $title;
		}
		else if($author_length > $max_line)
		{
			$lines = self::SpreadEvenly($title, 2, $max_line);
			$title_line1 = $lines[0];
			$title_line2 = $lines[1];
		}
		else
		{
			$lines = self::SpreadEvenly($total, 2, $max_line);
			$title_line1 = $lines[0];
			$title_line2 = $lines[1];
		}
		
		$this->title_line1 = $title_line1;
		$this->title_line2 = $title_line2;
	}
	
	public static function SpreadEvenly($content, $num_lines, $max_line)
	{
		$tokens = explode(" ", $content);
		$lines = array();
		$num_tokens = count($tokens);
		$num_displayed_tokens = 0;
		
		for($line = 0; $line < $num_lines; $line++)
		{
			$current_length = 0;
			$current_line = array();
			
			while(count($tokens))
			{
				$token_length = strlen($tokens[0]) + 1;	
				if(($token_length + $current_length) <= $max_line)
				{
					$current_length += $token_length;
					$current_line[] = array_shift($tokens);
					$num_displayed_tokens++;
				}
				else break;
			}
			
			$lines[$line] = implode(" ", $current_line);	
		}
		
		if($num_displayed_tokens < $num_tokens) 
		{
			$lines[count($lines) - 1] .= " ...";
		}
		
		return $lines;
	}
	
	private function SetCallNumberLines()
	{
		$parsed_call_number = self::ParseCallNumber($this->call_number);
		
		if(!$parsed_call_number)
		{
			$splitted_call_number = explode(" ", $this->call_number);
			$lines = array();
			$line_count = 0;
			foreach($splitted_call_number as $line)
			{
				if(isset($lines[$line_count])) $lines[$line_count] .= " " . $line;
				else $lines[$line_count] = $line;
				
				if(preg_match("!^[A-Z]{2}$!", $line)) continue;
				
				$line_count++;
			}
			
			$parsed_call_number = $lines;
		}
		
		foreach(range(1, 5) as $line_number)
		{
			$prop = "call_number_line$line_number";
			
			if(isset($parsed_call_number[$line_number - 1]))
			{
				$this->$prop = $parsed_call_number[$line_number - 1];
			}
			else $this->$prop = "";
		}
	}
	
	public function GetPrintCommand()
	{
		if($this->print_command != null) return $this->print_command;

		$this->Begin();
		
		$initial_statement = "^LL280^CI" . self::$ENCODING . "^CT%^XB^FO600,265^AC^FS^FO0,265^AC^FS^FO1010,265^AC^FS";
		// CT = Change prefix
		// LL = Label length
		// CI28: UTF-8
		// XB = Suppress backfeed
		
		$this->Add($initial_statement);
		
		$this->Add(new BookLabelLine($this->title_line1, 70, 32, "D"));
		$this->Add(new BookLabelLine($this->title_line2, 70, 54, "D"));
	
		$this->Add(new BookLabelLine($this->call_number_line1, 650, 22, "G"));
		$this->Add(new BookLabelLine($this->call_number_line2, 650, 94));
		$this->Add(new BookLabelLine($this->call_number_line3, 650, 130));
		$this->Add(new BookLabelLine($this->call_number_line4, 650, 178));
		$this->Add(new BookLabelLine($this->call_number_line5, 650, 222));

		$this->Add(new BookLabelBarcode($this->item_barcode, 70, 80));
		
		$this->Add(new BookLabelLine("*" . $this->item_barcode . "*", 70, 204, "E"));
		$this->Add(new BookLabelLine(self::$LIBRARY, 282, 212, "B"));
	
		$this->End();
	
		$this->print_command = implode(self::$LINE_SEPARATOR, $this->label_lines) . self::$LINE_SEPARATOR;
	
		return $this->print_command;
	}
	
	public function SetBarcode($barcode)
	{
		$this->item_barcode = $barcode;
	}
	
	public function SetTitle($title)
	{
		$this->title = $title;
	}
	
	public function SetAuthor($author)
	{
		$this->author = $author;
	}
	
	public function SetCallNumber($call_number)
	{
		$this->call_number = $call_number;
	}
	
	public static function SetLineSeparator($line_separator)
	{
		self::$LINE_SEPARATOR = $line_separator;
	}
	
	public static function GetLineSeparator()
	{
		return self::$LINE_SEPARATOR;
	}
	
	public static function SetEncoding($encoding)
	{
		self::$ENCODING = $encoding;
	}
	

	public static function ParseCallNumber($call_number)
	{
		if(preg_match(self::$CALL_NUMBER_REGEX, $call_number, $match))
		{
			/*$parsed_call_number= array(
					"fachkuerzel" => $match[1],
					"systemstelle" => $match[2],
					"systemstelle_zusatz" => isset($match[3]) ? $match[3] : "",
					"aufstellungsnr" => $match[4],
					"aufstellungsnr_zusatz" => isset($match[5]) ? $match[5] : ""
			);*/
			
			$parsed_call_number= array(
					0 => $match[1],
					1 => $match[2],
					2 => isset($match[3]) ? trim($match[3]) : "",
					3 => $match[4],
					4 => isset($match[5]) ? $match[5] : ""
			);
	
			return $parsed_call_number;
		}
		else return false;
	}
	
	public function GetPreviewImageSource()
	{
		$webservice_url_tpl = "http://api.labelary.com/v1/printers/8dpmm/labels/4.6x1.4/0/%s";
		
		$print_command = str_replace("%", urlencode("%"), $this->GetPrintCommand());
		$img_src= sprintf($webservice_url_tpl, $print_command);
		
		return $img_src;
	}
	
	public function __toString()
	{
		return $this->GetPrintCommand();
	}
	
}

?>