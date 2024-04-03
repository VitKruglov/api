<?php
//-------------------------------------------------------------//
//                                                             //
//       наборы расширенных и клиентозависымых возможностей исполнителей   //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class lpSkill
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "lp_skill";

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
    function lpSkillInfo()
    {
        include_once 'lpSkill/lpSkillInfo.php';   //информация о клиенте по ID
    }
    function lpSkillAll()
    {
        include 'lpSkill/lpSkillAll.php';   //информация о всех клиентах
    }
    function lpSkillInfoPut()
    {
        include_once 'lpSkill/lpSkillInfoPut.php';   //изменени информация о клиенте
    }
    function lpSkillAdd()
    {
        include_once 'lpSkill/lpSkillAdd.php';   //добавление набора требований
    }
    function lpSkillDelete()
    {
        include_once 'lpSkill/lpSkillDelete.php';   //удаление набора
    }
/*
    function lpSkillOneAdd()
    {
        include_once 'lpSkill/lpSkillOneAdd.php';   //добавление требования в набор
    }
    function lpSkillWorkerAdd()
    {
        include_once 'lpSkill/lpSkillWorkerAdd.php';   //добавление навыка исполнителю
    }
    function lpSkillDelete()
    {
        include_once 'lpSkill/lpSkillDelete.php';   //удаление набора
    }
    function lpSkillOneDelete()
    {
        include_once 'lpSkill/lpSkillOneDelete.php';   //удаление ребования из набора
    }
    function lpSkillWorkerDelete()
    {
        include_once 'lpSkill/lpSkillWorkerDelete.php';   //удаление навыка исполнителя
    }
    function lpSkillChange()
    {
        include_once 'lpSkill/lpSkillChange.php';   //добавление требования в набор
    }
    */
}
if(!$call_from_api){
    $objName="lpSkill";
    $lpSkill = new lpSkill($db);

    // одинаковая часть для всех object файлов
    include_once 'objectFileAll.php';
}
?>
