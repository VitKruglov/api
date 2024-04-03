<?php
//-------------------------------------------------------------//
//                                                             //
//                           заявка                             //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class requests
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "orders_requests";

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
    function requestInfo()
    {
        include_once 'requests/requestInfo.php';   //информация о клиенте по ID
    }
    function requestAll()
    {
        include 'requests/requestAll.php';   //информация о всех клиентах
    }
    function requestInfoPut()
    {
        include_once 'requests/requestInfoPut.php';   //изменени информация о клиенте
    }
    function requestAdd()
    {
        include_once 'requests/requestAdd.php';   //добавление клиента
    }
    function requestDelete()
    {
        include_once 'requests/requestDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="requests";
    $requests = new requests($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
