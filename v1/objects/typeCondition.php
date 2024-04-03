<?php
//-------------------------------------------------------------//
//                                                             //
//               справочник состояний лидов                    //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class typeCondition
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "type_condition";



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
    function typeConditionInfo()
    {
        include_once 'typeCondition/typeConditionInfo.php';   //информация о клиенте по ID
    }
    function typeConditionAll()
    {
        include 'typeCondition/typeConditionAll.php';   //информация о всех клиентах
    }
    function typeConditionInfoPut()
    {
        include_once 'typeCondition/typeConditionInfoPut.php';   //изменени информация о клиенте
    }
    function typeConditionAdd()
    {
        include_once 'typeCondition/typeConditionAdd.php';   //добавление клиента
    }
    function typeConditionDelete()
    {
        include_once 'typeCondition/typeConditionDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="typeCondition";
    $typeCondition = new typeCondition($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
