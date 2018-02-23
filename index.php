<?php

//phpinfo(); die();

require("config.php");
$action = isset($_GET['action']) ? $_GET['action'] : "";

switch ($action) {
  case 'archive':
    archive();
    break;
  case 'viewArticleSubcategory';
      viewArticleSubcategory();
      break;
  case 'viewArticle':
    viewArticle();
    break;
  default:
    homepage();
}

function archive() 
{
    $results = [];
    
    $categoryId = (isset($_GET['categoryId']) && $_GET['categoryId']) ? (int)$_GET['categoryId'] : null;
    
    $results['category'] = Category::getById($categoryId);
       
    $data = Article::getList(100000, $categoryId, 1);
    
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    
    foreach ($data['results'] as $category) {
        $results['categories'][$category->id] = $category;
    }
    
    $results['pageHeading'] = $results['category'] ?  $results['category']->name : "Article Archive";
    $results['pageTitle'] = $results['pageHeading'] . " | Widget News";
    
    require(TEMPLATE_PATH . "/archive.php");
}

/**
 * Вывод всех статей заданной подкатегории
 */
function viewArticleSubcategory() {
    $results = [];

    $subcategoryId = (isset($_GET['subcategoryId']) && $_GET['subcategoryId']) ? (int) $_GET['subcategoryId'] : null;

    $results['subcategory'] = Subcategory::getById($subcategoryId);

    $data = Article::getList(100000, null, 1, $subcategoryId);

    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];

    $data = Subcategory::getList();
    $results['subcategories'] = array();

    foreach ($data['results'] as $subcategory) {
        $results['subcategories'][$subcategory->id] = $subcategory;
    }

    $results['pageHeading'] = $results['subcategory'] ? $results['subcategory']->name : "Article Archive";
    $results['pageTitle'] = $results['pageHeading'] . " | Widget News";

    require(TEMPLATE_PATH . "/viewArticleSubcategory.php");
}

/**
 * Загрузка страницы с конкретной статьёй
 * 
 * @return null
 */
function viewArticle() 
{   
    if (!isset($_GET["articleId"]) || !$_GET["articleId"]) {
      homepage();
      return;
    }

    $results = array();
    $results['article'] = Article::getById((int)$_GET["articleId"]);
    $results['category'] = Category::getById($results['article']->categoryId);
    $results['pageTitle'] = $results['article']->title . " | Простая CMS";
    
    require(TEMPLATE_PATH . "/viewArticle.php");
}

/**
 * Вывод домашней ("главной") страницы сайта
 */
function homepage() 
{
    $results = array();
    $data = Article::getList(HOMEPAGE_NUM_ARTICLES, null, 1);
    $results['articles'] = $data['results'];
    $results['totalRows'] = $data['totalRows'];
    
    $data = Category::getList();
    $results['categories'] = array();
    foreach ( $data['results'] as $category ) { 
        $results['categories'][$category->id] = $category;
    } 
    
    $data = Subcategory::getList();
    $results['subcategories'] = array();
    foreach ($data['results'] as $subcategory) {
        $results['subcategories'][$subcategory->id] = $subcategory;
    }

    $results['pageTitle'] = "Простая CMS на PHP";
    
    require(TEMPLATE_PATH . "/homepage.php");
    
}