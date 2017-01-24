<?php 
class PrintJob
{
	private $printer;
	private $content;
	private $file;
	private $command = null;
	private $failed = false;
	
	public function __construct($content, $printer, $custom = false)
	{
		$this->content = $content;
		$this->printer = $printer;
		
		$date = date("Y-m-d");
		if($custom) $date .= $custom;
		$this->file = Config::Get("LABEL_FILE_PATH") . "labels_$date" . ".job";
		
		$file_written = $this->WriteFile();
		
		if($file_written) $this->command = "lp -d $this->printer $this->file";
		else $this->command = "file not written";
	}
	
	private function WriteFile()
	{
		return file_put_contents($this->file, $this->content);
	}
	
	public function Execute($do_not_print = false)
	{
		if(!$do_not_print) 
		{
			$output = exec($this->command . " 2>&1", $output);
			
			if(preg_match("!^lp\:!", $output))
			{
				$this->failed = true;
				$this->LogError($output);
			}
			
			return $output;
		}
		else return "debug, not executed.";
	}
	
	public function GetCommand()
	{
		return $this->command;
	}
	
	public function Failed()
	{
		return $this->failed;
	}
	
	private function LogError($error)
	{
		$log_file = Config::Get("ERROR_LOG_PATH");
		
		$timestamp = date("Y-m-d H:i:s");
		$success = file_put_contents($log_file, $timestamp . " - ". $error . " - PRINTER:" . $this->printer . "\n", FILE_APPEND);
		
		return $success;
	}
}
?>