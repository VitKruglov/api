<?php
//-------------------------------------------------------------//
//                                                             //
//                    лиды                                     //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

class lpLead
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_lead";

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
    function lpLeadInfo()
    {
        include_once 'lpLead/lpLeadInfo.php';   //информация о клиенте по ID
    }
    function lpLeadAll()
    {
        include 'lpLead/lpLeadAll.php';   //информация о всех клиентах
    }
    function lpLeadInfoPut()
    {
        include_once 'lpLead/lpLeadInfoPut.php';   //изменени информация о клиенте
    }
    function lpLeadAdd()
    {
        include_once 'lpLead/lpLeadAdd.php';   //добавление клиента
    }
    function lpLeadDelete()
    {
        include_once 'lpLead/lpLeadDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="lpLead";
    $lpLead = new lpLead($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
