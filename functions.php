<?php
// Дополнительные функции, пока оставил в корне, в дальнейшем можно переложить
// функция отладки
    function debug($param)
    {
        echo "<br><pre>";
        var_dump($param);
        echo "<pre><br>";
    }
  
// функция проверки паролей
    function testMatches($param)
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
// по указанному логину выбираем из базы пароль и актив
        $sql = "SELECT password, active FROM users WHERE login = :login";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $param['userName'], PDO::PARAM_STR);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
// сравниваем пароль из базы с введенным паролем
// если пароль совпадает, то возвращаем значение активности, 1 - активен, 0 - нет
        if ($param['password'] === $row['password']){
                if ($row['active'] == 1) {
                    return 'active';
                } else {
                    return 'noActive';
                }
            
        }
// если пароль не совпадает возвращаем false
        return false;
    }
