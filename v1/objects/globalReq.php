<?php
//-------------------------------------------------------------//
//                                                             //
//                 расширенные и клиентозависымые требования   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class globalReq
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "global_requirements";

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
    function globalReqInfo()
    {
        include_once 'globalReq/globalReqInfo.php';   //информация о клиенте по ID
    }
    function globalReqAll()
    {
        include 'globalReq/globalReqAll.php';   //информация о всех клиентах
    }
    function globalReqInfoPut()
    {
        include_once 'globalReq/globalReqInfoPut.php';   //изменени информация о клиенте
    }
    function globalReqAdd()
    {
        include_once 'globalReq/globalReqAdd.php';   //добавление клиента
    }
    function globalReqDelete()
    {
        include_once 'globalReq/globalReqDelete.php';   //удаление клиента
    }
    
}
if(!$call_from_api){
    $objName="globalReq";
    $globalReq = new globalReq($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
