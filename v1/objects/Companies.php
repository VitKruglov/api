<?php
//-------------------------------------------------------------//
//                                                             //
//                организации АК                               //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class Company
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "companies";

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
    function CompanyInfo()
    {
        include_once 'Companies/CompanyInfo.php';   //информация о клиенте по ID
    }
    function CompanyAll()
    {
        include 'Companies/CompanyAll.php';   //информация о всех клиентах
    }
    function CompanyInfoPut()
    {
        include_once 'Companies/CompanyInfoPut.php';   //изменени информация о клиенте
    }
    function CompanyAdd()
    {
        include_once 'Companies/CompanyAdd.php';   //добавление клиента
    }
    function CompanyDelete()
    {
        include_once 'Companies/CompanyDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="Company";
    $Company = new Company($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
