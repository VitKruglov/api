<?php
//-------------------------------------------------------------//
//                                                             //
//                           смены                             //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class Shift
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "orders_shifts";

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
    function shiftInfo()
    {
        include_once 'shifts/shiftInfo.php';   //информация о клиенте по ID
    }
    function shiftAll()
    {
        include_once 'shifts/shiftAll.php';   //информация о всех клиентах
    }
    function shiftInfoPut()
    {
        include_once 'shifts/shiftInfoPut.php';   //изменени информация о клиенте
    }
    function shiftAdd()
    {
        include_once 'shifts/shiftAdd.php';   //добавление клиента
    }
    function shiftDelete()
    {
        include_once 'shifts/shiftDelete.php';   //удаление клиента
    }
    function shiftPlan()
    {
        include_once 'shifts/shiftPlan.php';   //запланировано
    }
    function shiftRemove()
    {
        include_once 'shifts/shiftRemove.php';   //снято
    }
}

$objName="shift";
$shift = new Shift($db);


// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
