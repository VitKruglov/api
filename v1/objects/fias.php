<?php
//-------------------------------------------------------------//
//                                                             //
//                  работа с адресами ФИАС и dadata            //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class fias
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "fias";

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
    function fiasSearch()
    {
        include_once 'fias/fiasSearch.php';   //поиск объекта по адресу
    }
    function fiasAdd()
    {
        include_once 'fias/fiasAdd.php';   //добавление объекта в базу адресов и привязка к сущности
    }
    function fiasInfo()
    {
        include_once 'fias/fiasInfo.php';   //информация о клиенте по ID
    }
    function fiasAll()
    {
        include_once 'fias/fiasAll.php';   //информация о всех клиентах
    }
    function fiasInfoPut()
    {
        include_once 'fias/fiasInfoPut.php';   //изменени информация о клиенте
    }
    function fiasDelete()
    {
        include_once 'fias/fiasDelete.php';   //удаление клиента
    }
}

$objName="fias";
$fias = new fias($db);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
