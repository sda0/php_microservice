<?php
require 'vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';

$worker = Sda\Queue::getInstance();
$library = Sda\BooksLibrary::getInstance();

$run = function ($book) use ($library) {
    echo "Message from queue: ";

	$pid = pcntl_fork();
	switch($pid) {
		case -1:
			trigger_error('Не удалось породить дочерний процесс для '.$path,E_USER_WARNING);
	    case 0:
		    echo "{$book->action} {$book->path}\n";

		    $library->{$book->action}($book->path);
	    	break;
	    default: 
	    	break;
	}

};

$worker->msg_receive($run);