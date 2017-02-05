<?php

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Redis;

/*
*/

$app->get('/', function () use ($app) {
    return $app->version();
});

//Количество книг в библиотеке
$app->get('books/count', function ()  {
        return response()->json( Redis::get('books:count') );
});

//Список книг в библиотеке
$app->get('books/list', function ()  {
        return response()->json( Redis::lrange('books:list',0,-1) );
});

//Частота вхождения слова во всех книгах.
$app->get('words/{word}', function ($word)  {
        return response()->json( Redis::zrevrank('word',$word) );
});
//Частота вхождения слова в определенной книге.
$app->get('book/{book}/{word}', function ($book,$word)  {
        return response()->json( Redis::zrevrank('book:'.$book.'.txt',$word) );
});
//Общее количество слов во всех книгах.
$app->get('words', function ()  {
        return response()->json( Redis::get('words') );
});
//Общее количество слов в определенной книге.
$app->get('book/{book}', function ($book)  {
        return response()->json( Redis::get('words:'.$book.'.txt') );
});

//$app->get('post',      ['uses' => 'ExampleController@total']);
