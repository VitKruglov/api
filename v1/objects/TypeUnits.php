<?php
//-------------------------------------------------------------//
//                                                             //
//                        единицы измерений                    //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class TypeUnits
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "clients_type_units";



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
    function TypeUnitInfo()
    {
        include_once 'TypeUnits/TypeUnitInfo.php';   //информация о клиенте по ID
    }
    function TypeUnitAll()
    {
        include 'TypeUnits/TypeUnitAll.php';   //информация о всех клиентах
    }
    function typeUnitInfoPut()
    {
        include_once 'TypeUnits/TypeUnitInfoPut.php';   //изменени информация о клиенте
    }
    function typeUnitAdd()
    {
        include_once 'TypeUnits/TypeUnitAdd.php';   //добавление клиента
    }
    function TypeUnitDelete()
    {
        include_once 'TypeUnits/TypeUnitDelete.php';   //удаление клиента
    }
}
if(!$call_from_api){
    $objName="TypeUnits";
    $TypeUnits = new TypeUnits($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
