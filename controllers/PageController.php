<?php

/**
 * Created by PhpStorm.
 * User: gebruiker
 * Date: 07.09.16
 * Time: 14:55
 */
class PageController extends Controller
{
    public function actionIndex()
    {

        $item = Item::findOne(7);

       //$item->delete();

        return $this->render('index');
    }



}