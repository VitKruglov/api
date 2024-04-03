<?php
//-------------------------------------------------------------//
//                                                             //
//                 счета физического лица                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class LpAccount
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_account";

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
    function lpAccountInfo()
    {
        include_once 'lpAccount/lpAccountInfo.php';   //информация о клиенте по ID
    }
    function lpAccountAll()
    {
        include 'lpAccount/lpAccountAll.php';   //информация о всех клиентах
    }
    function lpAccountInfoPut()
    {
        include_once 'lpAccount/lpAccountInfoPut.php';   //изменени информация о клиенте
    }
    function lpAccountAdd()
    {
        include_once 'lpAccount/lpAccountAdd.php';   //добавление клиента
    }
    function lpAccountDelete()
    {
        include_once 'lpAccount/lpAccountDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="lpAccount";
    $lpAccount = new LpAccount($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
