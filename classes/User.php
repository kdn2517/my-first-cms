<?php
/*
* Класс для обработки пользователей
*/
class User
    {
    /**
    * @var int id пользователя
    */
    public $id = null;
    /**
    * @var string логин пользователя
    */
    public $login = null;
    /**
    * @var string пароль
    */
    public $password = null;
    /**
    * @var int активность пользователя: 1 - доступ открыт, 0 - доступ закрыт
    */
    public $active = null;
    
    public function __construct($data = [])
    {
        if (isset($data['id'])) {
            $this->id = (int) $data['id'];
        }
        
        if (isset($data['login'])) {
            $this->login = $data['login'];
        }
        
        if (isset($data['password'])) {
            $this->password = $data['password'];
        }
        
        if (isset($data['active'])) {
            $this->active = $data['active'];
        } else {
            $this->active = 0;
        }
    }
/**
 * функция для показа всех пользователей
 * return array (array 'results' => информация о всех пользователях, 'totalRows'
 * => кольичество пользователей)
 */    
    public static function getList() 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        
        $sql = "SELECT SQL_CALC_FOUND_ROWS * FROM users";
        
        $st = $conn->query($sql);
        
        $list = array();

        while ($row = $st->fetch()) {    
            $user = new User($row);
            $list[] = $user;
        }
        
        $sql = "SELECT FOUND_ROWS() AS totalRows";
        $totalRows = $conn->query($sql)->fetch();
        $conn = null;
        return (array(
            "results" => $list, 
            "totalRows" => $totalRows[0]
            ) 
        );
    }
/**
 * поиск пользователя по id
 * @param id id пользователя
 * @return User 
 */
    
    public static function getById($id) 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * FROM users WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_STR);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new User($row);
        }
    }
    
    public static function getByLogin($login) 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT * FROM users WHERE login = :login";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $login, PDO::PARAM_STR);
        $st->execute();

        $row = $st->fetch();
        $conn = null;
        
        if ($row) { 
            return new User($row);
        }
    }

 /**
  * функция для изменения свойств пользователя
  * @param array $params введенные данные формы
  * @return true если введенный логин уже занят, ничего не возвращает (return 
  * null), если логин не занят, изменения сохранены
  */
    public function update($params) 
    {        
/*
 * если меняется логин необходимо проверить на существование такого логина: если
 * логин занят, то пишем это, изменения не сохраняем. Если логин не менялся или
 * менялся и его новое значение уникально - сохраняем.
 */        
      if ($params['login'] != $params['userLogin'] && 
          $this->getByLogin($params['login'])) {
 // логин изменился, но новое значение уже занято - прерываем исполнение функции,
 // возвращаем true для обозначения этого факта
              return true;
      }
      // Обновляем
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $sql = "UPDATE users SET login=:login, password=:password, active=:active "
              . "WHERE login = :userLogin";
      
      $st = $conn->prepare($sql);
      $st->bindValue(":login", $this->login, PDO::PARAM_STR);
      $st->bindValue(":password", $this->password, PDO::PARAM_STR);
      $st->bindValue(":active", $this->active, PDO::PARAM_INT);
      $st->bindValue(":userLogin", $params['userLogin'], PDO::PARAM_STR);
      $st->execute();
      $conn = null;
    }
/**
  * функция для изменения свойств пользователя
  * @param array $params введенные данные формы
  * @return true если введенный логин уже занят, ничего не возвращает (return 
  * null), если логин не занят, изменения сохранены
  */    
    public function insert($params) 
    {
// проверяем логин на уникальность - если такого логина нет в базе - сохраняем, 
// если найден - пишем об этом. Возвращаем true, если логин занят.
        if ($this->getByLogin($params['login'])) {
            return true;
        }
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "INSERT INTO users (login, password, active) VALUES (:login, "
                . ":password, :active)";
        $st = $conn->prepare($sql);
        $st->bindValue(":login", $this->login, PDO::PARAM_STR);
        $st->bindValue(":password", $this->password, PDO::PARAM_STR);
        $st->bindValue(":active", $this->active, PDO::PARAM_INT);
        $st->execute();
        $conn = null;
    }
 /**
  * Удаление пользователя
  */   
    public function delete() 
    {
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $st = $conn->prepare("DELETE FROM users WHERE login = :login LIMIT 1");
      $st->bindValue(":login", $this->login, PDO::PARAM_STR);
      $st->execute();
      $st = $conn->prepare("DELETE FROM users_aritcles WHERE user = :id");
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    }
}

