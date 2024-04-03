<?php
//-------------------------------------------------------------//
//                                                             //
//                  доки физического лица                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpDoc
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_doc";

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
    function lpDocInfo()
    {
        include_once 'lpDoc/lpDocInfo.php';   //информация о клиенте по ID
    }
    function lpDocAll()
    {
        include 'lpDoc/lpDocAll.php';   //информация о всех клиентах
    }
    function lpDocInfoPut()
    {
        include_once 'lpDoc/lpDocInfoPut.php';   //изменени информация о клиенте
    }
    function lpDocAdd()
    {
        include_once 'lpDoc/lpDocAdd.php';   //добавление клиента
    }
    function lpDocDelete()
    {
        include_once 'lpDoc/lpDocDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="lpDoc";
    $lpDoc = new lpDoc($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
