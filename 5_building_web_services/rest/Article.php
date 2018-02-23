<?php
/* 
A domain Class to demonstrate RESTful web services
*/
Class Article {

      	private $ids;
	private $titles;

	private $disease="Asthma";
	
      	private function readFiles(){

	        $filename = $this->disease.".txt";
      		$handle = fopen($filename, "r");
      		$contents = fread($handle, filesize($filename));
      		$this->ids = explode("\n",$contents);
      		fclose($handle);

		$filename = $this->disease."Titles.txt";
      		$handle = fopen($filename, "r");
      		$contents = fread($handle, filesize($filename));
      		$t = explode("\n",$contents);
      		fclose($handle);
	
		$this->titles=array_combine($this->ids,$t);
	}
      
		
	public function getAllArticle(){
		$this->readFiles();
		return $this->ids;
	}
	
	public function getArticle($id){
		$this->readFiles();
		return array($id => $this->titles[$id]);
	}	
}
?>
