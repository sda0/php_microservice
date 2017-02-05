<?php
namespace Sda;

class Book{
	private $file;
	private $title;
	private $text;
	private $wordsrank = [];
	private $wordscount = 0;

	private function load(){
		if(empty($this->text))  {
			$delim = " \n\t,.!?:;";

			$this->title = $this->file->getFilename();
			$this->text = file_get_contents( $this->file->getRealPath() );
			$tok = strtok($this->text, $delim );

			while ($tok !== false) {
				$this->wordscount++;
				@ $this->wordsrank[$tok] = 1 + $this->wordsrank[$tok];
			    $tok = strtok($delim);
			}
		}
	}

	public function __construct(string $filepath){

		$this->file = new \SplFileInfo($filepath);
		if( ! $this->file->isReadable()){
			throw new Exception("File $filepath is not found or is not readable");
		}
		$this->load();
	}

	public function getWordsrank(){
		return $this->wordsrank;
	}

	public function getTitle(){
		return $this->title;
	}

	public function getWordsCount(){
		return $this->wordscount;
	}

}