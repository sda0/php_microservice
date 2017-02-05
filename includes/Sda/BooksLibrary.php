<?php
namespace Sda;

use Illuminate\Support\Facades\Redis as Redis;

class BooksLibrary {
    use SingletonTrait;
    
	function push(string $bookfilepath){
		
		$book = new Book($bookfilepath);
		$title = $book->getTitle();
		$words = $book->getWordsRank();
		$wordscount = $book->getWordsCount();
		


		
		//Количество книг в библиотеке
		Redis::incr("books:count");
		
		//Список книг в библиотеке
		Redis::rpush("books:list",$title);
			
		//Общее количество слов во всех книгах.
		Redis::incrby("words",(int)$wordscount);
		
		//Общее количество слов в определенной книге.
		Redis::set("words:$title",(int)$wordscount);
/*
		//Частота вхождения слова в определенной книге.
		foreach ($words as $word => $score) {
			Redis::command('zadd', ["book:$title", (int)$score, (string)$word]);
		}
		//Частота вхождения слова во всех книгах.
		foreach($words as $word => $score)
			Redis::zincrby("word",$word,$score);
*/
	}
	function pull(string $bookfilepath){

		$title = basename($bookfilepath);
		//$words = $book->getWordsRank();
		$wordscount = Redis::get("words:$title");
		
		
		//Количество книг в библиотеке
		Redis::decr("books:count");
		
		//Список книг в библиотеке
		Redis::lrem("books:list",0,$title);
			
		//Общее количество слов во всех книгах.
		Redis::decrby("words",(int)$wordscount);
		
		//Общее количество слов в определенной книге.
		Redis::del("words:$title");
/*
		//Частота вхождения слова в определенной книге.
		foreach ($words as $word => $score) {
			Redis::command('zadd', ["book:$title", (int)$score, (string)$word]);
		}
		//Частота вхождения слова во всех книгах.
		foreach($words as $word => $score)
			Redis::zincrby("word",$word,$score);
*/
	}



}