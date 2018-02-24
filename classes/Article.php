<?php


/**
 * Класс для обработки статей
 */
class Article
{
    // Свойства
    /**
    * @var int ID статей из базы данны
    */
    public $id = null;

    /**
    * @var int Дата первой публикации статьи
    */
    public $publicationDate = null;

    /**
    * @var string Полное название статьи
    */
    public $title = null;

     /**
    * @var int ID категории статьи
    */
    public $categoryId = null;

    /**
    * @var int ID подкатегории статьи
    */
    public $subcategoryId = null;
    
    /**
    * @var authors[] ID авторы статьи
    */
    public $authors = array();
    
    /**
    * @var string Краткое описание статьи
    */
    public $summary = null;

    /**
    * @var string HTML содержание статьи
    */
    public $content = null;
    /**
    * @var string первые 50 символов статьи
    */
    public $content50char = null;
    /**
    * @var int активность статьи (1 - статья активна, показывается на главной
    * странице; 0 - статья не активна, видит только админ)
    */
    public $activeArticle = null;

    
    /**
     * Создаст объект статьи
     * 
     * @param array $data массив значений (столбцов) строки таблицы статей
     * @param array $authors массив id авторов статьи, в конструкторе происходит
     *  перебор всех связей и остается только авторы даннай статьи
     */
    public function __construct($data=array(), $authors=array())
    {        
      if (isset($data['id'])) {
          $this->id = (int) $data['id'];
      }
      
      if (isset( $data['publicationDate'])) {
          $this->publicationDate = (string) $data['publicationDate'];     
      }

      //die(print_r($this->publicationDate));

      if (isset($data['title'])) {
          $this->title = $data['title'];        
      }
      
      if (isset($data['categoryId'])) {
          $this->categoryId = (int) $data['categoryId'];      
      }
      
      if (isset($data['subcategoryId'])) {
          $this->subcategoryId = (int) $data['subcategoryId'];      
      }
// делаем перебор, подходящих оставляем      
      if (isset($authors)) {
        foreach($authors as $author){
            if(isset($author['article'])) {
                if ($author['article'] == $this->id) {
                    $this->authors[] = $author['user'];
                } 
            } else { 
                $this->authors[] = $author;
            }
        }
      }
      
      if (isset($data['summary'])) {
          $this->summary = $data['summary'];         
      }
      
      if (isset($data['content'])) {
          $this->content = $data['content'];
          $this->content50char = mb_substr($data['content'], 0, 50) . '...';
      }
      
      if (isset($data['active'])) {
          $this->activeArticle = $data['active'];
      } else {
          $this->activeArticle = 0;
      }

    }

    /**
    * Устанавливаем свойства с помощью значений формы редактирования записи в 
     * заданном массиве
    *
    * @param assoc Значения записи формы
    */
    public function storeFormValues($params){
      // Сохраняем все параметры
      $this->__construct($params, $params['authors']);

      // Разбираем и сохраняем дату публикации
      if (isset($params['publicationDate'])) {
        $publicationDate = explode('-', $params['publicationDate']);

        if (count($publicationDate) == 3) {
          list ($y, $m, $d) = $publicationDate;
          $this->publicationDate = mktime (0, 0, 0, $m, $d, $y);
        }
      }
    }


    /**
    * Возвращаем объект статьи соответствующий заданному ID статьи
    *
    * @param int ID статьи
    * @return Article|false Объект статьи или false, если запись не найдена или 
    * возникли проблемы
    */

    public static function getById($id, $authors = array()) 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "SELECT *, UNIX_TIMESTAMP(publicationDate) "
                . "AS publicationDate FROM articles WHERE id = :id";
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $id, PDO::PARAM_INT);
        $st->execute();

        $row = $st->fetch();
// если массив авторов не известен он выберется в конструкторе, если известен - 
// оставляем как есть
        if(!isset($authors[0])){
            $sql = "SELECT * FROM users_articles";
            $query = $conn->prepare($sql);
            $query->execute();
            $autors = array();
            while($data = $query->fetch()){
                $authors[] = $data;
            }
            $conn = null;
        
            if ($row) { 
                return new Article($row, $authors);
            }
        }
        
        $conn = null;
        
        if ($row) { 
            return new Article($row);
        }
    }


    /**
    * Возвращает все (или диапазон) объекты Article из базы данных
    *
    * @param int $numRows Количество возвращаемых строк (по умолчанию = 1000000)
    * @param int $categoryId Вернуть статьи только из категории с указанным ID
    * @param string $order Столбец, по которому выполняется сортировка статей 
    * (по умолчанию = "publicationDate DESC")
    * @param int $useActiveValue отвечает за активность статьи: 1 - активна, 
    * видят все пользователи, 0 - не активна, видит только админ
    * @param int $author для вывода статей определенного автора
    * @return Array|false Двух элементный массив: results => массив объектов 
    * Article; totalRows => общее количество строк
    */
    public static function getList($numRows=1000000, 
                                   $categoryId=null,
                                   $useActiveValue = false,
                                   $subcategoryId=null,
                                   $author = null,
                                   $order="publicationDate DESC") 
    {
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
// если нужно вывести только статьи определенного автора, то добавляем условие
        if($author != null) {
            $clauseAuthor = "WHERE user = $author";
        } else {
            $clauseAuthor = "";
        }
// делаем запрос для отображения авторов статьи
        $sql = "SELECT * FROM users_articles $clauseAuthor";
        $query = $conn->prepare($sql);
        $query->execute();
        $autors = array();
        while($data = $query->fetch()){
            $authors[] = $data;
        }
// выбираем статьи одного автора
        if($author != null) {
            $clauseAuthors = " AND ";
            foreach($authors as $author) {
                $clauseAuthors .= "id=" . $author['article'] . " OR ";
            }
            $clauseAuthors = substr($clauseAuthors, 0, -3);
        }
// подстраиваем выборку для выборки подкатегорий. Так как каждая категория имеет
// категорию, то не имеет смысла фильтровать по категории и по подкатегории 
// одновременно. Поэтому так:
        if($useActiveValue === false) {
            if($categoryId) {
                $clause = "WHERE categoryId = :categoryId";
            } elseif($subcategoryId) {
                $clause = "wHERE subcategoryId = $subcategoryId";
            } else {
                $clause = "";
            }
        } else {
            if($categoryId) {
                $clause = "WHERE categoryId = :categoryId AND active = " . 
                                                                $useActiveValue;
            } elseif($subcategoryId) {
                $clause = "WHERE subcategoryId = $subcategoryId AND active = "
                        . "$useActiveValue";
            } else {
                if(isset($clauseAuthors)) {
// статьи одного автора мы не комбирируем с другими условиями, выводим только 
// активные
                    $clause = "WHERE active = " . $useActiveValue . 
                                                                 $clauseAuthors; 
                } else {
                    $clause = "WHERE active = " . $useActiveValue;
                }
            }
        }
       
        $sql = "SELECT SQL_CALC_FOUND_ROWS *, UNIX_TIMESTAMP(publicationDate) 
                AS publicationDate
                FROM articles $clause
                ORDER BY  $order  LIMIT :numRows";
        
        $st = $conn->prepare($sql);
        $st->bindValue(":numRows", $numRows, PDO::PARAM_INT);
        
        if ($categoryId) 
            $st->bindValue(":categoryId", $categoryId, PDO::PARAM_INT);
        
        $st->execute(); // выполняем запрос к базе данных
        $list = array();
// добавляем новое условие для конструктора(ПР5)        
        while ($row = $st->fetch()) {
            $article = new Article($row, $authors);
            $list[] = $article;
        }
        // Получаем общее количество статей, которые соответствуют критерию
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
    * Вставляем текущий объек Article в базу данных, устанавливаем его ID.
    */
    public function insert() 
    {

        // Есть уже у объекта Article ID?
        if (!is_null($this->id)) trigger_error("Article::insert(): Attempt to "
                . "insert an Article object that already has its ID property "
                . "set (to $this->id).", E_USER_ERROR);

        // Вставляем статью
        $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
        $sql = "INSERT INTO articles (publicationDate, categoryId, subcategoryId, title, "
                . "summary, content, active) VALUES (FROM_UNIXTIME(:publicationDate), "
                . ":categoryId, :subcategoryId, :title, :summary, :content, :active)";
        $st = $conn->prepare($sql);
        $st->bindValue(":publicationDate", $this->publicationDate, PDO::PARAM_INT);
        $st->bindValue(":categoryId", $this->categoryId, PDO::PARAM_INT);
        $st->bindValue(":subcategoryId", $this->subcategoryId, PDO::PARAM_INT);
        $st->bindValue(":title", $this->title, PDO::PARAM_STR);
        $st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
        $st->bindValue(":content", $this->content, PDO::PARAM_STR);
        $st->bindValue(":active", $this->activeArticle, PDO::PARAM_INT);
        $st->execute();
        $this->id = $conn->lastInsertId();
// вставляем новые строки в связующую таблицу        
        $st = $conn->prepare($sql);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);
        $st->execute();   
        foreach($this->authors as $user){
            $sql = "INSERT INTO users_articles (user, article) 
                    VALUES (:user, :id)";
            $st = $conn->prepare($sql);
            $st->bindValue(":user", $user, PDO::PARAM_INT);
            $st->bindValue(":id", $this->id, PDO::PARAM_INT);    
            $st->execute();
        }
        $conn = null;    
    }

    /**
    * Обновляем текущий объект статьи в базе данных
    */
    public function update() 
    {        
      // Есть ли у объекта статьи ID?
      if (is_null($this->id)) trigger_error("Article::update(): Attempt to "
              . "update an Article object that does not have its ID property "
              . "set.", E_USER_ERROR);

      // Обновляем статью
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $sql = "UPDATE articles SET publicationDate=FROM_UNIXTIME(:publicationDate),"
              . " categoryId=:categoryId, subcategoryId=:subcategoryId,"
              . " title=:title, summary=:summary, content=:content,"
              . " active=:active WHERE id = :id";
      $st = $conn->prepare($sql);
      $st->bindValue(":publicationDate", $this->publicationDate, PDO::PARAM_INT);
      $st->bindValue(":categoryId", $this->categoryId, PDO::PARAM_INT);
      $st->bindValue(":subcategoryId", $this->subcategoryId, PDO::PARAM_INT);
      $st->bindValue(":title", $this->title, PDO::PARAM_STR);
      $st->bindValue(":summary", $this->summary, PDO::PARAM_STR);
      $st->bindValue(":content", $this->content, PDO::PARAM_STR);
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->bindValue(":active", $this->activeArticle, PDO::PARAM_INT);
      $st->execute();
// вставляем новые строки в связующую таблицу      
      $sql = "DELETE FROM users_articles WHERE article = :id";
      $st = $conn->prepare($sql);
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
        
      foreach($this->authors as $user){
        $sql = "INSERT INTO users_articles (user, article) 
                VALUES (:user, :id)";
        $st = $conn->prepare($sql);
        $st->bindValue(":user", $user, PDO::PARAM_INT);
        $st->bindValue(":id", $this->id, PDO::PARAM_INT);    
        $st->execute();
      }
      $conn = null;
    }


    /**
    * Удаляем текущий объект статьи из базы данных
    */
    public function delete() 
    {

      // Есть ли у объекта статьи ID?
      if (is_null($this->id)) trigger_error("Article::delete(): Attempt to "
              . "delete an Article object that does not have its ID property "
              . "set.", E_USER_ERROR);

      // Удаляем статью
      $conn = new PDO(DB_DSN, DB_USERNAME, DB_PASSWORD);
      $st = $conn->prepare("DELETE FROM articles WHERE id = :id LIMIT 1");
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
// при удалении статьи удаляем все связи
      $st = $conn->prepare("DELETE FROM users_aritcles WHERE article = :id");
      $st->bindValue(":id", $this->id, PDO::PARAM_INT);
      $st->execute();
      $conn = null;
    }

}
