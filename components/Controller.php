<?php

    /**
 * Абстрактний клас Controller містить загальну логіку для контролерів
 */
 abstract class Controller
{
     /**
      * Редірект на головну сторінку
      */
    public function goHome()
    {
        header("Location: http://".$_SERVER['HTTP_HOST']);
    }

     /**
      * Редірект на заданий контролер
      * @param $link
      */
    public function redirect($link)
    {
        header("Location: http://".$_SERVER['HTTP_HOST'].$link);
    }

     /**
      * Метод який підключає відповідний шаблон, та передає в нього дані
      * @param $page
      * @param null $data
      * @return bool
      */
    public function render($page,$data = null)
    {
        if($data)
            extract($data);

        $nameClass = get_class($this);
        $nameAction = str_replace('Controller','',$nameClass);
        $nameDirectory = mb_strtolower($nameAction);

        include ROOT . '/views/layouts/header.php';
        require_once(ROOT . '/views/'.$nameDirectory.'/'.$page.'.php');
        include ROOT . '/views/layouts/footer.php';
        return true;
    }

}