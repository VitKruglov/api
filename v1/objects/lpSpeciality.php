<?php
//-------------------------------------------------------------//
//                                                             //
//                   специализации  исполнителя                 //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpSpeciality
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_speciality";

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
    function lpSpecialityInfo()
    {
        include_once 'lpSpeciality/lpSpecialityInfo.php';   //информация о клиенте по ID
    }
    function lpSpecialityAll()
    {
        include 'lpSpeciality/lpSpecialityAll.php';   //информация о всех клиентах
    }
    function lpSpecialityInfoPut()
    {
        include_once 'lpSpeciality/lpSpecialityInfoPut.php';   //изменени информация о клиенте
    }
    function lpSpecialityAdd()
    {
        include_once 'lpSpeciality/lpSpecialityAdd.php';   //добавление клиента
    }
    function lpSpecialityDelete()
    {
        include_once 'lpSpeciality/lpSpecialityDelete.php';   //удаление клиента
    }

}
if(!$call_from_api){
    $objName="lpSpeciality";
    $lpSpeciality = new lpSpeciality($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
