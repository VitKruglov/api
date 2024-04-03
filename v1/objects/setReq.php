<?php
//-------------------------------------------------------------//
//                                                             //
//       наборы расширенных и клиентозависымых требований   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class setReq
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "set_requirements";

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
    function setReqInfo()
    {
        include_once 'setReq/setReqInfo.php';   //информация о клиенте по ID
    }
    function setReqAll()
    {
        include 'setReq/setReqAll.php';   //информация о всех клиентах
    }
    function setReqInfoPut()
    {
        include_once 'setReq/setReqInfoPut.php';   //изменени информация о клиенте
    }
    function setReqAdd()
    {
        include_once 'setReq/setReqAdd.php';   //добавление набора требований
    }
    function setRequirementAdd()
    {
        include_once 'setReq/setRequirementAdd.php';   //добавление требования в набор
    }
    function setReqDelete()
    {
        include_once 'setReq/setReqDelete.php';   //удаление набора
    }
    function setRequirementDelete()
    {
        include_once 'setReq/setRequirementDelete.php';   //удаление ребования из набора
    }
    function setRequirementChange()
    {
        include_once 'setReq/setRequirementChange.php';   //добавление требования в набор
    }
}
if(!$call_from_api){
    $objName="setReq";
    $setReq = new setReq($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
