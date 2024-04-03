<?php
//-------------------------------------------------------------//
//                                                             //
//                         контрагенты                         //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class contractors
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "contractor";



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
    function contractorInfo()
    {
        include_once 'contractors/contractorInfo.php';   //информация о клиенте по ID
    }
    function contractorAll()
    {
        include 'contractors/contractorAll.php';   //информация о всех клиентах
    }
    function contractorInfoPut()
    {
        include_once 'contractors/contractorInfoPut.php';   //изменени информация о клиенте
    }
    function contractorAdd()
    {
        include_once 'contractors/contractorAdd.php';   //добавление клиента
    }
    function contractorDelete()
    {
        include_once 'contractors/contractorDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="contractors";
    $contractors = new contractors($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
