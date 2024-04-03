<?php
//-------------------------------------------------------------//
//                                                             //
//                  исполнители                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpWorkers
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_worker";

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
    function lpWorkerInfo()
    {
        include_once 'lpWorkers/lpWorkerInfo.php';   //информация о клиенте по ID
    }
    function lpWorkerAll()
    {
        include 'lpWorkers/lpWorkerAll.php';   //информация о всех клиентах
    }
    function lpWorkerInfoPut()
    {
        include_once 'lpWorkers/lpWorkerInfoPut.php';   //изменени информация о клиенте
    }
    function lpWorkerAdd()
    {
        include_once 'lpWorkers/lpWorkerAdd.php';   //добавление клиента
    }
    function lpWorkerDelete()
    {
        include_once 'lpWorkers/lpWorkerDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="lpWorkers";
    $lpWorkers = new lpWorkers($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
