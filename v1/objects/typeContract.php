<?php
//-------------------------------------------------------------//
//                                                             //
//                        типы договоров                    //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class typeContract
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "type_contract";



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
    function typeContractInfo()
    {
        include_once 'typeContract/typeContractInfo.php';   //информация о клиенте по ID
    }
    function typeContractAll()
    {
        include 'typeContract/typeContractAll.php';   //информация о всех клиентах
    }
    function typeContractInfoPut()
    {
        include_once 'typeContract/typeContractInfoPut.php';   //изменени информация о клиенте
    }
    function typeContractAdd()
    {
        include_once 'typeContract/typeContractAdd.php';   //добавление клиента
    }
    function typeContractDelete()
    {
        include_once 'typeContract/typeContractDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="typeContract";
    $typeContract = new typeContract($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
