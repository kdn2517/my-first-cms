# MY FIRST CMS

*Учебный проект "Простая CMS на базе PHP и MySQL" Подробные пояснения, пошаговая инструкция по написанию данной CMS, а также рекомендации для дальнейшей работы можно найти на сайте It For Free: http://fkn.ktu10.com/?q=node/9428*
## Как развернуть:

   1) Клонируем репозиторий на свой компьютер:
        - в директории /var/www/ в терминале вводим 
        ```
        git clone git@github.com:it-for-free/my-first-cms.git
        ```

   2) Создаёте виртуальный хост: http://fkn.ktu10.com/?q=node/8593

   3) Открываем проект в своей программе для разработки (например, NetBeans)

   4) Разворачиваем дамп базы данных:
        - сначала создайте в mysql новую базу данных с имененем `cms`
        - а потом разверните в ней дамп из файла `db_cms.sql` (лежит в корне данного проекта): http://fkn.ktu10.com/?q=node/8944

   5) Создаёте в корне проекта файл `config-local.php` и добавьте в неко как минимум такое содержимое (укажите пароль к бд):
      ```php
        <?php

        // вместо 1234 укажите свой пароль к базе данных
        $CmsConfiguration["DB_PASSWORD"] = "1234"; // переопределяем пароль к базе данных
       ```

   6) Следуем инструкциям http://fkn.ktu10.com/?q=node/9428

Удачной разработки!

Практическое задание 2
Для ПР2 добавляем новый столбец в таблицу:
```sql
ALTER TABLE articles ADD active TINYINT NOT NULL DEFAULT '1' 
COMMENT 'active отвечает за активность статьи: 
1 - активна, видят все пользователи, 
0 - не активна, видит только админ' AFTER content;
```

ПР3
```sql
CREATE TABLE users (login VARCHAR(25) NOT NULL , password VARCHAR(25) NOT NULL, 
active TINYINT NOT NULL DEFAULT '0', PRIMARY KEY (login)) COMMENT 'таблица 
пользователей - логин и пароль. Active - актуальность записи (разрешен ли вход 
по этому логину), редактируется админом';
```

ПР4
Создаю таблицу подкатегорий
```sql
CREATE TABLE cms.subcategories (id SMALLINT(5) NOT NULL AUTO_INCREMENT, name
VARCHAR(255) NOT NULL, description TEXT NOT NULL, category SMALLINT(5) NOT NULL 
DEFAULT '777', PRIMARY KEY (id)) ENGINE = MyISAM CHARSET=utf8 COLLATE
utf8_general_ci COMMENT 'таблица подкатегорий - номер, название, краткое 
описание и к какой категории относится (по умолчанию - без категории)';
```

Вставляю соответствующую категорию с id 777 и именем 'без категории':
```sql
INSERT INTO categories (id, name, description) VALUES ('7777', 'без категории', '')
```

Вставляю столбец с подкатегориями в таблицу со статьями:
```sql
ALTER TABLE articles ADD subcategoryId SMALLINT(5) NOT NULL DEFAULT '777' AFTER 
categoryId
```
ПР5
Для выполнения этого задания для начала добавим в таблицу users столбец id. Для 
этого с начала удалим первичный ключ с другого столбца.
```sql
ALTER TABLE users DROP PRIMARY KEY;
```
```sql
ALTER TABLE users ADD id INT(10) NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (id);
```
Теперь создаем новую таблицу связи статья - пользователь. Первичным ключем 
выступить стороннее значение.
```sql
CREATE TABLE cms.users_articles (id SMALLINT(10) NOT NULL AUTO_INCREMENT, 
user SMALLINT(10) NOT NULL, article SMALLINT(10) NOT NULL, PRIMARY KEY(id)) 
ENGINE = MyISAM;
```

меняем движек:
```sql
ALTER TABLE users_articles engine=InnoDB;
```
устанавливаем связь;
```sql
ALTER TABLE `users_articles` ADD INDEX( `user`);
```
```sql
ALTER TABLE `users` ADD UNIQUE( `id`);
```
```sql
ALTER TABLE `users_articles` ADD INDEX( `article`);
```
```sql
ALTER TABLE `articles` ADD UNIQUE( `id`);
```
```sql
ALTER TABLE users_articles ADD FOREIGN KEY (user) REFERENCES users (id) ON DELETE RESTRICT ON UPDATE RESTRICT ;
```
```sql
ALTER TABLE users_articles ADD FOREIGN KEY (article) REFERENCES articles (id) ON DELETE RESTRICT ON UPDATE RESTRICT ;
```
