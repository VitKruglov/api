<?php
//-------------------------------------------------------------//
//                                                             //
//                           роли                           //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class Unit
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "unit_name";



    // свойства объекта
    public $method;
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
    function unitInfo()
    {
        include_once 'units/unitInfo.php';   //информация о клиенте по ID
    }
    function unitAll()
    {
        include_once 'units/unitAll.php';   //информация о всех клиентах
    }
    function unitInfoPut()
    {
        include_once 'units/unitInfoPut.php';   //изменени информация о клиенте
    }
    function unitAdd()
    {
        include_once 'units/unitAdd.php';   //добавление клиента
    }
    function unitDelete()
    {
        include_once 'units/unitDelete.php';   //удаление клиента
    }
}

$objName="unit";
$unit = new Unit($db);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
