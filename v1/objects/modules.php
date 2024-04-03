<?php
//-------------------------------------------------------------//
//                                                             //
//                         аутсорсеры                          //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class modules
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "modules";



    // свойства объекта
    public $method;
    public $urlData;
    public $urlParam;
    public $post;
    public $result;         //результат выполения Mysql запроса

    // конструктор для соединения с базой данных
    public function __construct($db, $config)
    {
        $this->conn = $db;
        $this->config = $config;
        $this->Input = new Input($db);
    }

    //подключаем файл конкретного запроса
    function moduleInfo()
    {
        include_once 'modules/moduleInfo.php';   //информация о клиенте по ID
    }
    function moduleAll()
    {
        include_once 'modules/moduleAll.php';   //информация о всех клиентах
    }
    function moduleInfoPut()
    {
        include_once 'modules/moduleInfoPut.php';   //изменени информация о клиенте
    }
    function moduleAdd()
    {
        include_once 'modules/moduleAdd.php';   //добавление клиента
    }
    function moduleDelete()
    {
        include_once 'modules/moduleDelete.php';   //удаление клиента
    }
    function moduleTblPut()
    {
        include_once 'modules/moduleTblPut.php';   //исправление таблиц модуля
    }
}

$objName="modules";
$modules = new modules($db, $config);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
