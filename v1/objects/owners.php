<?php
//-------------------------------------------------------------//
//                                                             //
//                         аутсорсеры                          //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class Owner
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "owner";



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
    function ownerInfo()
    {
        include_once 'owners/ownerInfo.php';   //информация о клиенте по ID
    }
    function ownerAll()
    {
        include_once 'owners/ownerAll.php';   //информация о всех клиентах
    }
    function ownerInfoPut()
    {
        include_once 'owners/ownerInfoPut.php';   //изменени информация о клиенте
    }
    function ownerAdd()
    {
        include_once 'owners/ownerAdd.php';   //добавление клиента
    }
    function ownerDelete()
    {
        include_once 'owners/ownerDelete.php';   //удаление клиента
    }
}

$objName="owner";
$owner = new Owner($db);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
