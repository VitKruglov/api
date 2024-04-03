<?php
//-------------------------------------------------------------//
//                                                             //
//                           логи                      //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class log
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "log";

    // свойства объекта
    public $method;
    public $timezone;
    public $urlData;
    public $urlParam;
    public $post;
    public $result;         //результат выполения Mysql запроса

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
        $this->Input = new Input($db);
    }

    //подключаем файл конкретного запроса
    function logInfo()
    {
        include_once 'log/logInfo.php';   //информация о клиенте по ID
    }
    function logAll()
    {
        include 'log/logAll.php';   //информация о всех клиентах
    }
    function logInfoPut()
    {
        include_once 'log/logInfoPut.php';   //изменени информация о клиенте
    }
    function logAdd()
    {
        include_once 'log/logAdd.php';   //добавление клиента
    }
    function logDelete()
    {
        include_once 'log/logDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="log";
    $log = new log($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
