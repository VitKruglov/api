<?php
//-------------------------------------------------------------//
//                                                             //
//                          аутентификация пользователя        //
//                                                             //
//-------------------------------------------------------------//

// необходимые HTTP-заголовки
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");


class Auth
{
    // подключение к базе данных и таблице "products"
    private $conn;
    private $Input;
    private $table_name = "persons";



    // свойства объекта
    public $method;
    public $urlData;
    public $urlParam;
    public $postParam;
    public $formdata;
    public $result;         //результат выполения Mysql запроса

    // конструктор для соединения с базой данных
    public function __construct($db)
    {
        $this->conn = $db;
        $this->Input = new Input($db);
    }

    //подключаем файл конкретного запроса
    function authLogin()
    {
        include_once 'auth/authLogin.php';   //проверка логина пароля
    }
    function authLogout()
    {
        include_once 'auth/authLogout.php';   //logout
    }
    function authRefresh()
    {
        include_once 'auth/authRefresh.php';   //обновление токенов
    }
}

$objName="auth";
$auth = new Auth($db);

// одинаковая часть для всех object файлов
include_once 'objectFileAll.php';
?>
