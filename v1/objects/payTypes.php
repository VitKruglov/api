<?php
//-------------------------------------------------------------//
//                                                             //
//                           типы платежей                      //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class payTypes
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "pay_type";

    // свойства объекта
    public $method;
    public $timezone;
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
    function payTypeInfo()
    {
        include_once 'payTypes/payTypeInfo.php';   //информация о клиенте по ID
    }
    function payTypeAll()
    {
        include 'payTypes/payTypeAll.php';   //информация о всех клиентах
    }
    function payTypeInfoPut()
    {
        include_once 'payTypes/payTypeInfoPut.php';   //изменени информация о клиенте
    }
    function payTypeAdd()
    {
        include_once 'payTypes/payTypeAdd.php';   //добавление клиента
    }
    function payTypeDelete()
    {
        include_once 'payTypes/payTypeDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="payTypes";
    $payTypes = new payTypes($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
