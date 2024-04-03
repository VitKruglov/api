<?php
//-------------------------------------------------------------//
//                                                             //
//                   договора  исполнителя                 //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpContracts
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_contract";

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
    function lpContractInfo()
    {
        include_once 'lpContracts/lpContractInfo.php';   //информация о клиенте по ID
    }
    function lpContractAll()
    {
        include 'lpContracts/lpContractAll.php';   //информация о всех клиентах
    }
    function lpContractInfoPut()
    {
        include_once 'lpContracts/lpContractInfoPut.php';   //изменени информация о клиенте
    }
    function lpContractAdd()
    {
        include_once 'lpContracts/lpContractAdd.php';   //добавление клиента
    }
    function lpContractDelete()
    {
        include_once 'lpContracts/lpContractDelete.php';   //удаление клиента
    }

}

if(!$call_from_api){
    $objName="lpContracts";
    $lpContracts = new lpContracts($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
