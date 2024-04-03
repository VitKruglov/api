<?php
//-------------------------------------------------------------//
//                                                             //
//                          детали выплат                      //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class payDetail
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "pay_detail";

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
    function payDetailAll()
    {
        include 'payDetail/payDetailAll.php';   //информация о клиенте по ID
    }
    function payDetailInfo()
    {
        include_once 'payDetail/payDetailInfo.php';   //информация о клиенте по ID
    }
    function payDetailInfoPut()
    {
        include_once 'payDetail/payDetailInfoPut.php';   //изменени информация о клиенте
    }
    function payDetailAdd()
    {
        include_once 'payDetail/payDetailAdd.php';   //добавление клиента
    }
    function payDetailDelete()
    {
        include_once 'payDetail/payDetailDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="payDetail";
    $payDetail = new payDetail($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
