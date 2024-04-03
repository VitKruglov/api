<?php
//-------------------------------------------------------------//
//                                                             //
//                           физические лица                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpMan
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_man";

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
    function lpManInfo()
    {
        include_once 'lpMan/lpManInfo.php';   //информация о клиенте по ID
    }
    function lpManAll()
    {
        include 'lpMan/lpManAll.php';   //информация о всех клиентах
    }
    function lpManInfoPut()
    {
        include_once 'lpMan/lpManInfoPut.php';   //изменени информация о клиенте
    }
    function lpManPhotoPut()
    {
        include_once 'lpMan/lpManPhotoPut.php';   //загрузка фото физ.лица
    }
    function lpManPhotoGet()
    {
        include_once 'lpMan/lpManPhotoGet.php';   //получение фото физ.лица
    }
    function lpManAdd()
    {
        include_once 'lpMan/lpManAdd.php';   //добавление клиента
    }
    function lpManDelete()
    {
        include_once 'lpMan/lpManDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="lpMan";
    $lpMan = new lpMan($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
