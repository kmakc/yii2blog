<?php

namespace app\modules\admin\controllers;


use app\models\Comment;
use yii\base\Controller;

class CommentController extends Controller
{
    public function actionIndex()
    {
        $comments = Comment::find()->orderBy('id desc')->all();

        return $this->render('index', ['comments' => $comments]);
    }
}