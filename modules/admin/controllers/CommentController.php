<?php
namespace app\modules\admin\controllers;

use app\models\Comment;
use yii\web\Controller;

/**
 * Class CommentController
 * @package app\modules\admin\controllers
 */
class CommentController extends Controller
{
    public function actionIndex()
    {
        $comments = Comment::find()->orderBy('id desc')->all();

        return $this->render('index',['comments'=>$comments]);
    }

    /**
     * @param $id
     * @return \yii\web\Response
     * @throws \Exception
     * @throws \Throwable
     *
     * @return $this|\yii\web\Response
     */
    public function actionDelete($id)
    {
        /* @var Comment $comment */
        $comment = Comment::findOne($id);
        if ($comment->delete()) {
            return $this->redirect(['comment/index']);
        }

        return $this;
    }

    /**
     * @param $id
     * @return $this|\yii\web\Response
     */
    public function actionAllow($id)
    {
        /* @var Comment $comment */
        $comment = Comment::findOne($id);
        if ($comment->allow()) {
            return $this->redirect(['index']);
        }

        return $this;
    }

    /**
     * @param $id
     * @return $this|\yii\web\Response
     */
    public function actionDisallow($id)
    {
        /* @var Comment $comment */
        $comment = Comment::findOne($id);
        if ($comment->disallow()) {
            return $this->redirect(['index']);
        }

        return $this;
    }
}
