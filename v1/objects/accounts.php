<?php
//-------------------------------------------------------------//
//                                                             //
//                 счета АК                                    //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class accounts
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "accounts";

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
    function accountInfo()
    {
        include_once 'accounts/accountInfo.php';   //информация о клиенте по ID
    }
    function accountAll()
    {
        include 'accounts/accountAll.php';   //информация о всех клиентах
    }
    function accountInfoPut()
    {
        include_once 'accounts/accountInfoPut.php';   //изменени информация о клиенте
    }
    function accountAdd()
    {
        include_once 'accounts/accountAdd.php';   //добавление клиента
    }
    function accountDelete()
    {
        include_once 'accounts/accountDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="accounts";
    $accounts = new accounts($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
