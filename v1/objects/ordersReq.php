<?php
//-------------------------------------------------------------//
//                                                             //
//                  требования к ЛП                //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class ordersReq
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "orders_requirements";

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
    function orderReqInfo()
    {
        include_once 'ordersReq/orderReqInfo.php';   //информация о клиенте по ID
    }
    function orderReqAll()
    {
        include 'ordersReq/orderReqAll.php';   //информация о всех клиентах
    }
    function orderReqInfoPut()
    {
        include_once 'ordersReq/orderReqInfoPut.php';   //изменени информация о клиенте
    }
    function orderReqAdd()
    {
        include_once 'ordersReq/orderReqAdd.php';   //добавление клиента
    }
    function orderReqDelete()
    {
        include_once 'ordersReq/orderReqDelete.php';   //удаление клиента
    }
    
}
if(!$call_from_api){
    $objName="ordersReq";
    $ordersReq = new ordersReq($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
