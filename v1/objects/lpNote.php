<?php
//-------------------------------------------------------------//
//                                                             //
//                  комменты физического лица                   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpNote
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_note";

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
    function lpNoteInfo()
    {
        include_once 'lpNote/lpNoteInfo.php';   //информация о клиенте по ID
    }
    function lpNoteAll()
    {
        include 'lpNote/lpNoteAll.php';   //информация о всех клиентах
    }
    function lpNoteInfoPut()
    {
        include_once 'lpNote/lpNoteInfoPut.php';   //изменени информация о клиенте
    }
    function lpNoteAdd()
    {
        include_once 'lpNote/lpNoteAdd.php';   //добавление клиента
    }
    function lpNoteDelete()
    {
        include_once 'lpNote/lpNoteDelete.php';   //удаление клиента
    }
}

if(!$call_from_api){
    $objName="lpNote";
    $lpNote = new lpNote($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
