<?php

/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 09.09.16
 * Time: 16:03
 */
class ErrorController extends Controller
{
    public function action404()
    {
        return $this->render('404');
    }
}