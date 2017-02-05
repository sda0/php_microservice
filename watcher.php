<?php

require 'vendor/autoload.php';


$queue = Sda\Queue::getInstance();


$loop = React\EventLoop\Factory::create();
$inotify = new MKraemer\ReactInotify\Inotify($loop);

$inotify->add(__DIR__.'/books', IN_CLOSE_WRITE | IN_DELETE | IN_MOVED_FROM | IN_MOVED_TO );


$bookpush = function ($path) use (&$queue) {
	$book = (object) [ 'action'=>'push', 'path' => $path ];
	$queue->msg_send( $book );
};

$bookpull = function ($path) use (&$queue) {
	$book = (object) [ 'action'=>'pull', 'path' => $path ];
	$queue->msg_send( $book );
};

$inotify->on(IN_CLOSE_WRITE, $bookpush );
$inotify->on(IN_MOVED_TO, $bookpush );
$inotify->on(IN_DELETE, $bookpull );
$inotify->on(IN_MOVED_FROM, $bookpull );

$loop->run();
