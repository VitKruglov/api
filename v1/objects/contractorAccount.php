<?php
//-------------------------------------------------------------//
//                                                             //
//                  счета контрагентов                         //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class contractorAccount
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "contractor_account";



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
    function contractorAccountInfo()
    {
        include_once 'contractorAccount/contractorAccountInfo.php';   //информация о клиенте по ID
    }
    function contractorAccountAll()
    {
        include 'contractorAccount/contractorAccountAll.php';   //информация о всех клиентах
    }
    function contractorAccountInfoPut()
    {
        include_once 'contractorAccount/contractorAccountInfoPut.php';   //изменени информация о клиенте
    }
    function contractorAccountAdd()
    {
        include_once 'contractorAccount/contractorAccountAdd.php';   //добавление клиента
    }
    function contractorAccountDelete()
    {
        include_once 'contractorAccount/contractorAccountDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="contractorAccount";
    $contractorAccount = new contractorAccount($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
