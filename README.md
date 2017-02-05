# php_microservice

установить redis 3.1.0 


    $ yum install redis

установить inotify, phpredis:

    $ pecl install inotify
    $ pecl install redis

Клонировать и инсталлировать приложение:

    $ git clone https://sda0@github.com/sda0/php_microservice.git
    $ php composer.phar install 

Запустить все скрипты приложения в фон: воркер, вотчер и веб для api

    $ php -f worker.php &
    $ php -f watcher.php &
    $ php -S localhost:8000 -t public &

Директория для книг: books

Для тестирования вызвать загрузку 60 книг:

    $ php -f generator.php
    
Если я ничего не забыл то  запросы ниже дадут статистику:

    http://localhost:8000/words
    http://localhost:8000/books/list
    http://localhost:8000/books/count
    http://localhost:8000/book/1
    http://localhost:8000/book/2
    
