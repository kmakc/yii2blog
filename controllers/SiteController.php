<?php
namespace app\controllers;

use Yii;
use yii\web\Response;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Article;
use app\models\Category;
use app\models\CommentForm;

/**
 * Class SiteController
 * @package app\controllers
 */
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only'  => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow'   => true,
                        'roles'   => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage
     *
     * @return string
     */
    public function actionIndex()
    {
        $articles        = Article::getAll();
        $popular         = Article::getPopular();
        $recent          = Article::getRecent();
        $categories      = Category::getAll();

        return $this->render('index', [
            'articles'    => $articles['articles'],
            'pagination'  => $articles['pagination'],
            'popular'     => $popular,
            'recent'      => $recent,
            'categories'  => $categories,
        ]);
    }

    /**
     * Displays single article
     *
     * @param  $id    Article id
     * @return string
     */
    public function actionView($id)
    {
        /* @var Article $article */
        $article         = Article::findOne($id);
        $popular         = Article::getPopular();
        $recent          = Article::getRecent();
        $categories      = Category::getAll();
        $comments        = $article->getArticleComments();
        $commentForm     = new CommentForm();

        $article->viewedCounter();

        return $this->render('single', [
            'article'     => $article,
            'popular'     => $popular,
            'recent'      => $recent,
            'categories'  => $categories,
            'comments'    => $comments,
            'commentForm' => $commentForm
        ]);
    }


    /**
     * Displays categories list
     *
     * @param $id     Category id
     * @return string
     */
    public function actionCategory($id)
    {
        $categoryData = Category::getArticlesByCategory($id);
        $popular      = Article::getPopular();
        $recent       = Article::getRecent();
        $categories   = Category::getAll();


        return $this->render('category', [
            'articles'   => $categoryData['articles'],
            'pagination' => $categoryData['pagination'],
            'popular'    => $popular,
            'recent'     => $recent,
            'categories' => $categories,
        ]);
    }

    /**
     * Save comment
     *
     * @param  $id      Article id
     * @return Response
     */
    public function actionPublishComment($id)
    {
        $model = new CommentForm();

        if (Yii::$app->request->isPost) {
            $model->load(Yii::$app->request->post());
            if ($model->saveComment($id)) {
                Yii::$app->getSession()->setFlash('success', 'Your comment will be added soon!');
                return $this->redirect(['site/view', 'id' => $id]);
            }
        }

        return $this->redirect(['site/view']);
    }
}
