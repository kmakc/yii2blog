<?php
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\Article;
use app\models\Comment;
use app\models\Category;
use app\models\CommentForm;

/* @var Article     $article */
/* @var Article[]   $popular */
/* @var Article[]   $recent */
/* @var Category[]  $categories */
/* @var Comment[]   $comments */
/* @var CommentForm $commentForm */

$this->title = 'Article | ' . $article->title;
?>
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <article class="post">
                    <div class="post-thumb">
                        <?= Html::img($article->getImage()) ?>
                    </div>
                    <div class="post-content">
                        <header class="entry-header text-center text-uppercase">
                            <h6><a href="<?= Url::toRoute(['site/category', 'id' => $article->category->id]) ?>"><?= $article->category->title ?></a></h6>
                            <h1 class="entry-title"><?= $article->title ?></h1>
                        </header>
                        <div class="entry-content">
                            <?= $article->content ?>
                        </div>
                        <!-- TODO: TAGS -->
                        <div class="decoration">
                            <a href="#" class="btn btn-default">Tag1</a>
                            <a href="#" class="btn btn-default">Tag2</a>
                        </div>
                        <!-- TODO: TAGS -->
                        <div class="social-share">
							<span
                                class="social-share-title pull-left text-capitalize">By <?= $article->author->name ?> On <?= $article->getDate() ?></span>
                            <ul class="text-center pull-right">
                                <li><a class="s-facebook" href="#"><i class="fa fa-facebook"></i></a></li>
                                <li><a class="s-twitter" href="#"><i class="fa fa-twitter"></i></a></li>
                                <li><a class="s-google-plus" href="#"><i class="fa fa-google-plus"></i></a></li>
                                <li><a class="s-linkedin" href="#"><i class="fa fa-linkedin"></i></a></li>
                                <li><a class="s-instagram" href="#"><i class="fa fa-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </article>
            <?= $this->render('/partials/_comment', [
                'article'     => $article,
                'comments'    => $comments,
                'commentForm' => $commentForm,
            ]) ?>
            </div>
            <?= $this->render('/partials/_sidebar', [
                'popular'    => $popular,
                'recent'     => $recent,
                'categories' => $categories,
            ]) ?>
        </div>
    </div>
</div>
