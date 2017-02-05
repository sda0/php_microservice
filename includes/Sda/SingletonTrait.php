<?php
namespace Sda;

trait SingletonTrait {
    static private $instance = null;

    private function __construct() { /* ... @return Singleton */ }  // Защищаем от создания через new Singleton
    private function __clone() { /* ... @return Singleton */ }  // Защищаем от создания через клонирование
    private function __wakeup() { /* ... @return Singleton */ }  // Защищаем от создания через unserialize

    static public function getInstance() {
		return 
		self::$instance===null
			? self::$instance = new static()//new self()
			: self::$instance;
    }
}