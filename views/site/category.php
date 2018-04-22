<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\data\Pagination;
use yii\widgets\LinkPager;
use app\models\Article;
use app\models\Category;

/* @var Article[]  $articles */
/* @var Pagination $pagination */
/* @var Article[]  $popular */
/* @var Article[]  $recent */
/* @var Category[] $categories */

$this->title = 'Categories list';
?>
<div class="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <?php foreach ($articles as $article): ?>
                    <article class="post post-list">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="post-thumb">
                                    <a href="<?= Url::toRoute(['site/view', 'id' => $article->id])?>"><?= Html::img($article->getImage(), ['class' => 'pull-left']) ?></a>
                                    <a href="<?= Url::toRoute(['site/view', 'id' => $article->id])?>" class="post-thumb-overlay text-center">
                                        <div class="text-uppercase text-center">View Post</div>
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="post-content">
                                    <header class="entry-header text-uppercase">
                                        <h6><a href="#"><?= $article->category->title ?></a></h6>
                                        <h1 class="entry-title"><a href="<?= Url::toRoute(['site/view', 'id' => $article->id])?>"><?= $article->title ?></a></h1>
                                    </header>
                                    <div class="entry-content">
                                        <p><?= $article->description ?></p>
                                    </div>
                                    <div class="social-share">
                                        <span class="social-share-title pull-left text-capitalize">By <?= $article->author->name ?> On <?= $article->getDate() ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                <?php endforeach ?>
                <?= LinkPager::widget(['pagination' => $pagination]) ?>
            </div>
            <?= $this->render('/partials/_sidebar', [
                'popular'    => $popular,
                'recent'     => $recent,
                'categories' => $categories,
            ]) ?>
        </div>
    </div>
</div>
