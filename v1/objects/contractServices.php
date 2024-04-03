<?php
//-------------------------------------------------------------//
//                                                             //
//                  услуги по договорам                         //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class contractServices
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "contract_services";



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
    function contractServicesInfo()
    {
        include_once 'contractServices/contractServicesInfo.php';   //информация о клиенте по ID
    }
    function contractServicesAll()
    {
        include 'contractServices/contractServicesAll.php';   //информация о всех клиентах
    }
    function contractServicesInfoPut()
    {
        include_once 'contractServices/contractServicesInfoPut.php';   //изменени информация о клиенте
    }
    function contractServicesAdd()
    {
        include_once 'contractServices/contractServicesAdd.php';   //добавление клиента
    }
    function contractServicesDelete()
    {
        include_once 'contractServices/contractServicesDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="contractServices";
    $contractServices = new contractServices($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
