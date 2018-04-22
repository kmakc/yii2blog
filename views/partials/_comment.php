<?php
use yii\helpers\Html;
use app\models\Comment;
use app\models\Article;
use app\models\CommentForm;
use yii\widgets\ActiveForm;

/* @var Comment[]   $comments */
/* @var Article     $article */
/* @var CommentForm $commentForm */
?>
<!-- comment list -->
<?php if (!empty($comments)): ?>
    <?php foreach ($comments as $comment): ?>
        <div class="bottom-comment">
            <div class="comment-img">
                <?= Html::img($comment->user->image, ['class' => 'img-circle']) ?>
            </div>
            <div class="comment-text">
                <h5><?= $comment->user->name ?></h5>
                <p class="comment-date">
                    <?= $comment->getDate() ?>
                </p>
                <p class="para"> <?= $comment->text ?> </p>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<!-- end comment list -->

<?php if (!Yii::$app->user->isGuest): ?>
    <div class="leave-comment">
        <?php $form = ActiveForm::begin([
            'action'  => ['site/publish-comment', 'id' => $article->id],
            'options' => ['class' => 'form-horizontal contact-form', 'role' => 'form']]) ?>
        <div class="form-group">
            <div class="col-md-12">
                <?= $form->field($commentForm, 'comment')->textarea(['class' => 'form-control', 'placeholder' => 'Write Message'])->label(false) ?>
            </div>
        </div>
        <?= Html::submitButton('Post Comment', ['class' => 'btn btn-success']) ?>
        <?php ActiveForm::end();?>
    </div>
<?php endif; ?>
