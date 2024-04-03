<?php
//-------------------------------------------------------------//
//                                                             //
//                  отделы                  //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class clientDepartment
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_department";

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
    function clientDepartmentInfo()
    {
        include_once 'clientsDepartment/clientDepartmentInfo.php';   //информация о клиенте по ID
    }
    function clientDepartmentAll()
    {
        include 'clientsDepartment/clientDepartmentAll.php';   //информация о всех клиентах
    }
    function clientDepartmentInfoPut()
    {
        include_once 'clientsDepartment/clientDepartmentInfoPut.php';   //изменени информация о клиенте
    }
    function clientDepartmentAdd()
    {
        include_once 'clientsDepartment/clientDepartmentAdd.php';   //добавление клиента
    }
    function clientDepartmentDelete()
    {
        include_once 'clientsDepartment/clientDepartmentDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="clientDepartment";
    $clientDepartment = new ClientDepartment($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
