<?php
//-------------------------------------------------------------//
//                                                             //
//                 ставки                 //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class clientsRate
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_rate";

    // свойства объекта
    public $method;
    public $urlData;
    public $urlParam;
    public $post;
    public $result;         //результат выполения Mysql запроса

    // конструктор для соединения с базой данных
    public function __construct($db=null)
    {
        $this->conn = $db;
        $this->Input = new Input($db);
    }

    //подключаем файл конкретного запроса
    function clientRateInfo()
    {
        include_once 'clientsRate/clientRateInfo.php';   //информация о клиенте по ID
    }
    function clientRateAll()
    {
        include 'clientsRate/clientRateAll.php';   //информация о всех клиентах
    }
    function clientRateInfoPut()
    {
        include_once 'clientsRate/clientRateInfoPut.php';   //изменени информация о клиенте
    }
    function clientRateAdd()
    {
        include_once 'clientsRate/clientRateAdd.php';   //добавление клиента
    }
    function clientRateDelete()
    {
        include_once 'clientsRate/clientRateDelete.php';   //удаление клиента
    }
  
}


// одинаковая часть для всех object файлов
if(!$call_from_api){
    $objName="clientsRate";
    $clientsRate = new clientsRate($db);

    include_once 'objectFileAll.php';
}


?>
