<?php
//-------------------------------------------------------------//
//                                                             //
//                 базовые требования к ставкам                //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class clientReq
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_requirements";

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
    function clientReqInfo()
    {
        include_once 'clientsReq/clientReqInfo.php';   //информация о клиенте по ID
    }
    function clientReqAll()
    {
        include 'clientsReq/clientReqAll.php';   //информация о всех клиентах
    }
    function clientReqInfoPut()
    {
        include_once 'clientsReq/clientReqInfoPut.php';   //изменени информация о клиенте
    }
    function clientReqAdd()
    {
        include_once 'clientsReq/clientReqAdd.php';   //добавление клиента
    }
    function clientReqDelete()
    {
        include_once 'clientsReq/clientReqDelete.php';   //удаление клиента
    }
    
}
if(!$call_from_api){
    $objName="clientReq";
    $clientReq = new clientReq($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
