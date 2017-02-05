<?php
namespace Sda;

/**
 * Обертка для System V сообщений в PHP, использует шаблон синглтон и генератор для обхода очереди сообщений
 * @author Denis Scheglov <denis.scheglov@gmail.com>
 * @example client.php Пример клиента
 * @example worker.php Пример сервера
 */
class Queue
{
    use SingletonTrait;

    /** @var int ID очереди */
    const QUEUE         = 21671;

    /** @var int Владелец очереди user id */
    const UID           = 10073;

    /** @var int Владелец очереди group id */
    const GID           = 505;

    /** @var int Крайние 9 бит, 438 - полный доступ, 384 - только владелец и рут  */
    const MODE          = 384; 


    /** @var int Нотификация msg_type */
    const TYPE_NOTIFY  = 3;

    /** @var int Входящий запрос msg_type */
    const TYPE_REQUEST  = 1;

    /** @var int Исходящий ответ msg_type */
    const TYPE_RESPONSE = 2;

    /** @var resource|null Ссылка на очередь    */
    private $queue      = null;

    /** @var int|string Крайняя ошибка    */
    public $errorcode   = 0;


    /**
     * Инициализация очереди и прав доступа к ней, описание прав есть в хидере <msgctl.h>
     * @return resource
     */
    protected function msg_get_queue()
    {
        if (!is_resource($this->queue)) {
            $this->queue = msg_get_queue(self::QUEUE);
            msg_set_queue($this->queue, array('msg_perm.uid' => self::UID, 'msg_perm.gid' => self::GID, 'msg_perm.mode' => self::MODE));
        }
        return $this->queue;
    }

    /**
     * Функция-генератор для обхода очереди сообщений
     * @return yield   
     */
    public function responses()
    {
        $msg_type     = null;
        $msg          = null;
        $max_msg_size = 512;

        $stat = msg_stat_queue($this->queue);

        while ($stat['msg_qnum']-- > 0 && msg_receive($this->queue, self::TYPE_RESPONSE, $msg_type, $max_msg_size, $msg)) {
            yield $msg;
            $msg_type = null;
            $msg      = null;
        }
    }

    /**
     * Обработчик сообщения для наследования класса. Переопределить при необходимости в наследниках.
     * @param var $msg 
     * @return void
     */
    protected function run( $msg ) 
    {
        return;
    }

    /**
     * @todo Включить деструктор, если нужно убивать очередь
     * @todo Проверить 
     */
    public function __destruct()
    {
        if (false && is_resource($this->queue)) {
            msg_remove_queue($this->queue);
        }
    }

    /**
     * Функция ожидает получение сообщения и вызывает callback функцию-обработчик
     * @param int $desiredmsgtype Какой тип сообщения слушаем
     * @param function $callback Выполняем эту функцию при получении сообщения
     * @return type
     */
    public function msg_receive($callback = null, int $desiredmsgtype = self::TYPE_NOTIFY)
    {
        $this->msg_get_queue();

        $msg_type     = null;
        $msg          = null;
        $max_msg_size = 512;
        while (msg_receive($this->queue, $desiredmsgtype, $msg_type, $max_msg_size, $msg)) {
            /** Обработка при наследовании */
            $this->run($msg);
            /** Обработка при замыкании */
            $callback($msg);

            $msg_type = null;
            $msg      = null;
        }
    }

    /**
     * Отправить сообщение в очередь
     * @param int $msgtype Тип сообщения
     * @param type $message Сообщение
     * @param bool|bool $serialize Сериализиировать или нет
     * @param bool|bool $blocking Блокировать если сообщение более 512 байт
     * @return type
     */
    public function msg_send($message, int $msgtype = self::TYPE_NOTIFY, bool $serialize = true, bool $blocking = true)
    {
        $this->msg_get_queue();
        return msg_send($this->queue, $msgtype, $message, $serialize, $blocking, $this->errorcode);
    }

    /**
     * Возвращает статистику использования очереди,
     * @return Array Массив со статистикой
     */
    public function msg_stat_queue()
    {
        return msg_stat_queue($this->queue);
    }

}