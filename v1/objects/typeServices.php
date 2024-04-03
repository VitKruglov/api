<?php
//-------------------------------------------------------------//
//                                                             //
//                  услуги по договорам                         //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class typeServices
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "type_services";



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
    function typeServicesInfo()
    {
        include_once 'typeServices/typeServicesInfo.php';   //информация о клиенте по ID
    }
    function typeServicesAll()
    {
        include 'typeServices/typeServicesAll.php';   //информация о всех клиентах
    }
    function typeServicesInfoPut()
    {
        include_once 'typeServices/typeServicesInfoPut.php';   //изменени информация о клиенте
    }
    function typeServicesAdd()
    {
        include_once 'typeServices/typeServicesAdd.php';   //добавление клиента
    }
    function typeServicesDelete()
    {
        include_once 'typeServices/typeServicesDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="typeServices";
    $typeServices = new typeServices($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
