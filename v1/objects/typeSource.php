<?php
//-------------------------------------------------------------//
//                                                             //
//               справочник источников информации              //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class typeSource
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "type_source";



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
    function typeSourceInfo()
    {
        include_once 'typeSource/typeSourceInfo.php';   //информация о клиенте по ID
    }
    function typeSourceAll()
    {
        include 'typeSource/typeSourceAll.php';   //информация о всех клиентах
    }
    function typeSourceInfoPut()
    {
        include_once 'typeSource/typeSourceInfoPut.php';   //изменени информация о клиенте
    }
    function typeSourceAdd()
    {
        include_once 'typeSource/typeSourceAdd.php';   //добавление клиента
    }
    function typeSourceDelete()
    {
        include_once 'typeSource/typeSourceDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="typeSource";
    $typeSource = new typeSource($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
