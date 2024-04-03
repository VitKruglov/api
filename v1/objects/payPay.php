<?php
//-------------------------------------------------------------//
//                                                             //
//                           выплаты                           //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class payPay
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "pay_pay";

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
    function payPayInfo()
    {
        include_once 'payPay/payPayInfo.php';   //информация о клиенте по ID
    }
    function payPayAll()
    {
        include 'payPay/payPayAll.php';   //информация о всех клиентах
    }
    function payPayInfoPut()
    {
        include_once 'payPay/payPayInfoPut.php';   //изменени информация о клиенте
    }
    function payPayAdd()
    {
        include_once 'payPay/payPayAdd.php';   //добавление клиента
    }
    function payPayDelete()
    {
        include_once 'payPay/payPayDelete.php';   //удаление клиента
    }
    function payPaySignature()
    {
        include_once 'payPay/payPaySignature.php';   //подтверждение выплаты
    }
}
if(!$call_from_api){
    $objName="payPay";
    $payPay = new payPay($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
