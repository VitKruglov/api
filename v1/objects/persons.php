<?php
//-------------------------------------------------------------//
//                                                             //
//                   пользователь платформой                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class persons
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "persons";

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
    function personInfo()
    {
        include_once 'persons/personInfo.php';   //информация о клиенте по ID
    }
    function personAll()
    {
        include 'persons/personAll.php';   //информация о всех клиентах
    }
    function personInfoPut()
    {
        include_once 'persons/personInfoPut.php';   //изменени информация о клиенте
    }
    function personAdd()
    {
        include_once 'persons/personAdd.php';   //добавление клиента
    }
    function personDelete()
    {
        include_once 'persons/personDelete.php';   //удаление клиента
    }

}
if(!$call_from_api){
    $objName="persons";
    $persons = new persons($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
