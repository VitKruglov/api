<?php
//-------------------------------------------------------------//
//                                                             //
//                  обекты                  //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class clientsObject
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_object";

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
    function clientObjectInfo()
    {
        include_once 'clientsObject/clientObjectInfo.php';   //информация о клиенте по ID
    }
    function clientObjectAll()
    {
        include 'clientsObject/clientObjectAll.php';   //информация о всех клиентах
    }
    function clientObjectInfoPut()
    {
        include_once 'clientsObject/clientObjectInfoPut.php';   //изменени информация о клиенте
    }
    function clientObjectAdd()
    {
        include_once 'clientsObject/clientObjectAdd.php';   //добавление клиента
    }
    function clientObjectDelete()
    {
        include_once 'clientsObject/clientObjectDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="clientsObject";
    $clientsObject = new clientsObject($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
