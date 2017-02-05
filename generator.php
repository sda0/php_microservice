<?php
    require 'vendor/autoload.php';

    $filename = 'words.txt';

    $loop = React\EventLoop\Factory::create();


    $loop->addPeriodicTimer(0.1, function ($timer) use ($filename) {
        static $counter=1;

        $resultfile = 'books/'.$counter.'.txt';

        $process = new React\ChildProcess\Process('RAND=$(($RANDOM%80+80)); for((i=1;i<=$RAND;i+=1)); do sed "${RANDOM}q;d" '.$filename.'; done >| '.$resultfile);

        $process->start($timer->getLoop());
  
        echo "$counter.txt wrote".PHP_EOL;
        if(++$counter>60) die();
    });


    $loop->run();
