<?php
//-------------------------------------------------------------//
//                                                             //
//                   специальности                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class specialties
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "speciality";

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
    function specialityInfo()
    {
        include_once 'specialties/specialityInfo.php';   //информация о клиенте по ID
    }
    function specialityAll()
    {
        include 'specialties/specialityAll.php';   //информация о всех клиентах
    }
    function specialityInfoPut()
    {
        include_once 'specialties/specialityInfoPut.php';   //изменени информация о клиенте
    }
    function specialityAdd()
    {
        include_once 'specialties/specialityAdd.php';   //добавление клиента
    }
    function specialityDelete()
    {
        include_once 'specialties/specialityDelete.php';   //удаление клиента
    }

}
if(!$call_from_api){
    $objName="specialties";
    $specialties = new specialties($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
