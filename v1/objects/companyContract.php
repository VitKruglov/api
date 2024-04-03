<?php
//-------------------------------------------------------------//
//                                                             //
//                  договора контрагентов                         //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class companyContract
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "company_contract";



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
    function companyContractInfo()
    {
        include_once 'companyContract/companyContractInfo.php';   //информация о клиенте по ID
    }
    function companyContractAll()
    {
        include 'companyContract/companyContractAll.php';   //информация о всех клиентах
    }
    function companyContractInfoPut()
    {
        include_once 'companyContract/companyContractInfoPut.php';   //изменени информация о клиенте
    }
    function companyContractAdd()
    {
        include_once 'companyContract/companyContractAdd.php';   //добавление клиента
    }
    function companyContractDelete()
    {
        include_once 'companyContract/companyContractDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="companyContract";
    $companyContract = new companyContract($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
