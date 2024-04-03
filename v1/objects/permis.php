<?php
//-------------------------------------------------------------//
//                                                             //
//                        права доступа                          //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class permis
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "permis_unit";



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
    function permisUnit()
    {
        include_once 'permis/permisUnit.php';   //права для ролей
    }
    function permisUnitPut()
    {
        include_once 'permis/permisUnitPut.php';   //изменение прав для ролей
    }
    function permisAll()
    {
        include_once 'permis/permisAll.php';   //информация о всех правах
    }
    function permisPerson()
    {
        include_once 'permis/permisPerson.php';   //права для ролей
    }
    function permisPersonPut()
    {
        include_once 'permis/permisPersonPut.php';   //права для ролей
    }
 /*   function permisInfo()
    {
        include_once 'permis/permisInfo.php';   //информация о клиенте по ID
    }
    function permisInfoPut()
    {
        include_once 'permis/permisInfoPut.php';   //изменени информация о клиенте
    }
    function permisAdd()
    {
        include_once 'permis/permisAdd.php';   //добавление клиента
    }
    function permisDelete()
    {
        include_once 'permis/permisDelete.php';   //удаление клиента
    }
    */
}

$objName="permis";
$permis = new permis($db);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
