<?php

require("config.php");
session_start();
$action = isset($_GET['action']) ? $_GET['action'] : "";
$username = isset($_SESSION['userName']) ? $_SESSION['userName'] : "";

if ($action != "login" && $action != "logout" && !$username) {
    login();
    exit;
}

switch ($action) {
    case 'login':
        login();
        break;
    case 'logout':
        logout();
        break;
    case 'newArticle':
        newArticle();
        break;
    case 'editArticle':
        editArticle();
        break;
    case 'deleteArticle':
        deleteArticle();
        break;
    case 'listUsers':
        listUsers();
        break;
    case 'newUser':
        newUser();
        break;
    case 'editUser':
        editUser();
        break;
    case 'deleteUser':
        deleteUser();
        break;
    case 'listCategories':
        listCategories();
        break;
    case 'newCategory':
        newCategory();
        break;
    case 'editCategory':
        editCategory();
        break;
    case 'deleteCategory':
        deleteCategory();
        break;
    default:
        listArticles();
}

/**
 * Авторизация пользователя -- установка значения в сессию
 */
function login() 
{
    $results = array();
    $results['pageTitle'] = "Admin Login | Widget News";

    if (isset($_POST['login'])) {

        // Пользователь получает форму входа: попытка авторизировать пользователя

        if ($_POST['userName'] == ADMIN_USERNAME && 
                                         $_POST['password'] == ADMIN_PASSWORD) {

          // Вход прошел успешно: создаем сессию и перенаправляем на страницу
          //  администратора
          $_SESSION['userName'] = ADMIN_USERNAME;
          header("Location: admin.php");
//если это не админ, проверяем соответствие логин-пароль с помощью testMatches. 
        } elseif ($active = testMatches($_POST)) {
//если пароль совпал, проверяем активность - если запись активни - записываем 
//имя в сессию и входим в админку, если запись не активна - выводим сообщение 
            if ($active == 'active') {
                $_SESSION['userName'] = $_POST['userName'];
                header("Location: admin.php");
            } else {
                
                $results['errorMessage'] = "Ваша учетная запись не активна "
                  . "обратитесь к администратору.";
          require(TEMPLATE_PATH . "/admin/loginForm.php");
            }

        } else {

          // Ошибка входа: выводим сообщение об ошибке для пользователя
          $results['errorMessage'] = "Incorrect username or password. Please "
                  . "try again.";
          require(TEMPLATE_PATH . "/admin/loginForm.php");
        }

    } else {

      // Пользователь еще не получил форму: выводим форму
      require(TEMPLATE_PATH . "/admin/loginForm.php");
    }

}


function logout() 
{
    unset($_SESSION['userName']);
    header("Location: admin.php");
}


function newArticle() 
{	  
    $results = array();
    $results['pageTitle'] = "New Article";
    $results['formAction'] = "newArticle";

    if (isset($_POST['saveChanges'])) {
        // Пользователь получает форму редактирования статьи: сохраняем новую 
        // статью
        $article = new Article();
        $article->storeFormValues($_POST);

//            А здесь данные массива $article уже неполные(есть только Число от 
//            даты, категория и полный текст статьи)          
        $article->insert();
        header("Location: admin.php?status=changesSaved");

    } elseif (isset($_POST['cancel'])) {

        // Пользователь сбросил результаты редактирования: возвращаемся к списку
        //  статей
        header("Location: admin.php");
    } else {

        // Пользователь еще не получил форму редактирования: выводим форму
        $results['article'] = new Article;
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require(TEMPLATE_PATH . "/admin/editArticle.php");
    }
}


/**
 * Редактирование статьи
 * 
 * @return null
 */
function editArticle() 
{	  
    $results = array();
    $results['pageTitle'] = "Edit Article";
    $results['formAction'] = "editArticle";

    if (isset( $_POST['saveChanges'])) {

        // Пользователь получил форму редактирования статьи: сохраняем изменения
        if (!$article = Article::getById((int)$_POST['articleId'])) {
            header("Location: admin.php?error=articleNotFound");
            return;
        }

        $article->storeFormValues($_POST);
        $article->update();
        header("Location: admin.php?status=changesSaved");

    } elseif (isset($_POST['cancel'])) {

        // Пользователь отказался от результатов редактирования: возвращаемся к 
        // списку статей
        header("Location: admin.php");
    } else {

        // Пользвоатель еще не получил форму редактирования: выводим форму
        $results['article'] = Article::getById((int)$_GET['articleId']);
        $data = Category::getList();
        $results['categories'] = $data['results'];
        require(TEMPLATE_PATH . "/admin/editArticle.php");
    }

}


function deleteArticle() 
{
    if (!$article = Article::getById((int)$_GET['articleId'])) {
        header("Location: admin.php?error=articleNotFound");
        return;
    }

    $article->delete();
    header("Location: admin.php?status=articleDeleted");
}


function listArticles() 
{
    $results = array();
    $data = Article::getList();
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $data = Category::getList();
    $results['categories'] = array();
    foreach ($data['results'] as $category) 
                              $results['categories'][$category->id] = $category;
    $results['pageTitle'] = "All Articles";

    if (isset($_GET['error'])) {
        if ($_GET['error'] == "articleNotFound") 
                         $results['errorMessage'] = "Error: Article not found.";
    }

    if (isset($_GET['status'])) {
        if ($_GET['status'] == "changesSaved") 
                    $results['statusMessage'] = "Your changes have been saved.";
        if ($_GET['status'] == "articleDeleted") 
                                 $results['statusMessage'] = "Article deleted.";
    }

    require( TEMPLATE_PATH . "/admin/listArticles.php" );
}

function listCategories() 
{
    $results = array();
    $data = Category::getList();
    $results['categories'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = "Article Categories";

    if (isset( $_GET['error'])) {
        if ($_GET['error'] == "categoryNotFound") 
                        $results['errorMessage'] = "Error: Category not found.";
        if ($_GET['error'] == "categoryContainsArticles")   
            $results['errorMessage'] = "Error: Category contains articles. "
                . "Delete the articles, or assign them to another category, "
                . "before deleting this category.";
    }

    if (isset($_GET['status'])) {
        if ($_GET['status'] == "changesSaved") 
                    $results['statusMessage'] = "Your changes have been saved.";
        if ($_GET['status'] == "categoryDeleted") 
                                $results['statusMessage'] = "Category deleted.";
    }

    require(TEMPLATE_PATH . "/admin/listCategories.php");
}
	  
	  
function newCategory() 
{
    $results = array();
    $results['pageTitle'] = "New Article Category";
    $results['formAction'] = "newCategory";

    if (isset($_POST['saveChanges'])) {

        // User has posted the category edit form: save the new category
        $category = new Category;
        $category->storeFormValues($_POST);
        $category->insert();
        header("Location: admin.php?action=listCategories&status=changesSaved");

    } elseif (isset($_POST['cancel'])) {

        // User has cancelled their edits: return to the category list
        header("Location: admin.php?action=listCategories");
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = new Category;
        require(TEMPLATE_PATH . "/admin/editCategory.php");
    }

}


function editCategory() 
{
    $results = array();
    $results['pageTitle'] = "Edit Article Category";
    $results['formAction'] = "editCategory";

    if (isset($_POST['saveChanges'])) {

        // User has posted the category edit form: save the category changes

        if (!$category = Category::getById((int)$_POST['categoryId'])) {
          header("Location: admin.php?action=listCategories&error=categoryNotFound");
          return;
        }

        $category->storeFormValues($_POST);
        $category->update();
        header("Location: admin.php?action=listCategories&status=changesSaved");

    } elseif (isset( $_POST['cancel'])) {

        // User has cancelled their edits: return to the category list
        header("Location: admin.php?action=listCategories");
    } else {

        // User has not posted the category edit form yet: display the form
        $results['category'] = Category::getById((int)$_GET['categoryId']);
        require(TEMPLATE_PATH . "/admin/editCategory.php");
    }

}


function deleteCategory() 
{
    if (!$category = Category::getById((int)$_GET['categoryId'])) {
        header("Location: admin.php?action=listCategories&error=categoryNotFound");
        return;
    }

    $articles = Article::getList(1000000, $category->id);

    if ($articles['totalRows'] > 0) {
        header("Location: admin.php?action=listCategories&error=categoryContainsArticles");
        return;
    }

    $category->delete();
    header("Location: admin.php?action=listCategories&status=categoryDeleted");
}



/*
 * Работа с пользователями
 */
  
/*
 * Показать всех пользователей
 */
function listUsers()
{
    $results = array();
    $data = User::getList();
    $results['users'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    $results['pageTitle'] = "Users";

    if (isset($_GET['error'])) {
        if ($_GET['error'] == "userNotFound") 
                            $results['errorMessage'] = "Error: User not found.";
        }

    if (isset($_GET['status'])) {
        if ($_GET['status'] == "changesSaved") 
                    $results['statusMessage'] = "Your changes have been saved.";
        if ($_GET['status'] == "userDeleted") 
                                    $results['statusMessage'] = "User deleted.";
    }

    require(TEMPLATE_PATH . "/admin/listUsers.php");  
}

/*
 * Новый пользователь
 */
function newUser()
{
    $results = array();
    $results['pageTitle'] = "New User";
    $results['formAction'] = "newUser";

    if (isset($_POST['saveChanges'])) {
        
        $user = new User($_POST);         
/*
 * если логин существует - пишем об этом, если нет - сохраняем
 */        
        if ($user->insert($_POST)) {
// логин занят
            header("Location: admin.php?action=newUser&error=loginExists");
        } else {
            header("Location: admin.php?action=listUsers&status=changesSaved");
        }
    } elseif (isset($_POST['cancel'])) {
        
        header("Location: admin.php?action=listUsers");
        
    } else {
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'loginExists') {
                $results['errorMessage'] = 'Логин занят';
            }
        }
        $results['user'] = new User;
        require(TEMPLATE_PATH . "/admin/editUser.php");
    }
}

/*
 * Изменить свойства пользователя
 */
function editUser()
{
    $results = array();
    $results['pageTitle'] = "Edit User";
    $results['formAction'] = "editUser";

    if (isset( $_POST['saveChanges'])) {

        $user = new User($_POST);
        
/*
 * если пользователь меняет логин на занятый выводим форму вновь, если логин 
 * не занят - сохраняем
 */
        if ($user->update($_POST)) {
 // логин занят
           header("Location: admin.php?action=editUser&error=loginExists&"
                   . "userLogin=" . $_POST['userLogin']); 
        } else {
           header("Location: admin.php?action=listUsers&status=changesSaved");
        }
    } elseif (isset($_POST['cancel'])) {

        header("Location: admin.php?action=listUsers");
    } else {
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'loginExists') {
                $results['errorMessage'] = 'Логин занят';
            }
        }
        $results['user'] = User::getByLogin($_GET['userLogin']);
        require(TEMPLATE_PATH . "/admin/editUser.php");
    }
}

/*
 * Удалить пользователя
 */
function deleteUser()
{
    if (!$user = User::getByLogin($_GET['userLogin'])) {
        header("Location: admin.php?action=listUsers&error=userNotFound");
        return;
    }

    $user->delete();
    header("Location: admin.php?action=listUsers&status=userDeleted"); 
}
