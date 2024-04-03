<?php
//-------------------------------------------------------------//
//                                                             //
//                           начисления                        //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class payBalance
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "pay_balance";

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
    function payBalanceNow()
    {
        include_once 'payBalance/payBalanceNow.php';   //текущий баланс
    }
    function payBalanceInfo()
    {
        include_once 'payBalance/payBalanceInfo.php';   //информация о клиенте по ID
    }
    function payBalanceAll()
    {
        include 'payBalance/payBalanceAll.php';   //информация о всех клиентах
    }
    function payBalanceInfoPut()
    {
        include_once 'payBalance/payBalanceInfoPut.php';   //изменени информация о клиенте
    }
    function payBalanceAdd()
    {
        include_once 'payBalance/payBalanceAdd.php';   //добавление клиента
    }
    function payBalanceDelete()
    {
        include_once 'payBalance/payBalanceDelete.php';   //удаление клиента
    }
    function payBalanceIdPay()
    {
        include_once 'payBalance/payBalanceIdPay.php';   //платежи исполнителя по id выплаты
    }
    function payBalanceSignature()
    {
        include_once 'payBalance/payBalanceSignature.php';   //утверждение платежа
    }
    function payBalanceRate()
    {
        include 'payBalance/payBalanceRate.php';   //вычсиление начисление по смене и ставке
    }
    function payBalanceRatePlan()
    {
        include 'payBalance/payBalanceRatePlan.php';   //вычсиление планируемого начисление по смене и ставке
    }
    function payBalanceRateShift()
    {
        include 'payBalance/payBalanceRateShift.php';   // вычисление суммы оплаты из полей смены по фактически введенным данным менеджером 
    }
}

if(!$call_from_api){
    $objName="payBalance";
    $payBalance = new payBalance($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
