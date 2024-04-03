<?php
//-------------------------------------------------------------//
//                                                             //
//                           клиент                            //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class clients
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients";



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
    function clientInfo()
    {
        include_once 'clients/clientInfo.php';   //информация о клиенте по ID
    }
    function clientAll()
    {
        include 'clients/clientAll.php';   //информация о всех клиентах
    }
    function clientInfoPut()
    {
        include_once 'clients/clientInfoPut.php';   //изменени информация о клиенте
    }
    function clientAdd()
    {
        include_once 'clients/clientAdd.php';   //добавление клиента
    }
    function clientDelete()
    {
        include_once 'clients/clientDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="clients";
    $clients = new clients($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
