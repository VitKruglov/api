<?php
//-------------------------------------------------------------//
//                                                             //
//                          группы обектов                     //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class clientsGroup
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_group";

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
    function clientGroupInfo()
    {
        include_once 'clientsGroup/clientGroupInfo.php';   //информация о клиенте по ID
    }
    function clientGroupAll()
    {
        include 'clientsGroup/clientGroupAll.php';   //информация о всех клиентах
    }
    function clientGroupInfoPut()
    {
        include_once 'clientsGroup/clientGroupInfoPut.php';   //изменени информация о клиенте
    }
    function clientGroupAdd()
    {
        include_once 'clientsGroup/clientGroupAdd.php';   //добавление клиента
    }
    function clientGroupDelete()
    {
        include_once 'clientsGroup/clientGroupDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="clientsGroup";
    $clientsGroup = new clientsGroup($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
