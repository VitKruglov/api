<?php
//-------------------------------------------------------------//
//                                                             //
//                           заказ                             //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class orders
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "orders";

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
    function orderInfo()
    {
        include_once 'orders/orderInfo.php';   //информация о клиенте по ID
    }
    function orderAll()
    {
        include $path.'orders/orderAll.php';   //информация о всех клиентах
    }
    function orderInfoPut()
    {
        include_once 'orders/orderInfoPut.php';   //изменени информация о клиенте
    }
    function orderAdd()
    {
        include_once 'orders/orderAdd.php';   //добавление клиента
    }
    function orderDelete()
    {
        include_once 'orders/orderDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="orders";
    $orders = new orders($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
