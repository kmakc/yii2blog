<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecordInterface;

/**
 * This is the model class for table "article".
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $content
 * @property string $date
 * @property string $image
 * @property int $viewed
 * @property int $user_id
 * @property int $status
 * @property int $category_id
 *
 * @property ArticleTag[] $articleTags
 * @property Comment[] $comments
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * Articles per page
     */
    const ARTICLES_PER_PAGE = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'article';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'required'],
            [['title', 'description', 'content'], 'string'],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['date'], 'default', 'value' => date('Y-m-d')],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'          => 'ID',
            'title'       => 'Title',
            'description' => 'Description',
            'content'     => 'Content',
            'date'        => 'Date',
            'image'       => 'Image',
            'viewed'      => 'Viewed',
            'user_id'     => 'User ID',
            'status'      => 'Status',
            'category_id' => 'Category ID',
        ];
    }

    /**
     * @return string
     */
    public function getImage()
    {
        return $this->image ?  '/uploads/' . $this->image : '/no-image.png';
    }

    /**
     * @param  $fileName
     * @return bool
     */
    public function saveImage($fileName)
    {
        $this->image = $fileName;
        return $this->save(false);
    }

    /**
     * @return $this
     */
    public function deleteImage()
    {
        /** @var ImageUpload $imageUploadModel */
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);

        return $this;
    }

    /**
     * @return bool
     */
    public function beforeDelete()
    {
        $this->deleteImage();
        return parent::beforeDelete();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * @param  int $categoryId
     * @return bool
     */
    public function saveCategory($categoryId)
    {
        /** @var ActiveRecordInterface $category */
        $category = Category::findOne($categoryId);
        if ($category != null) {
            $this->link('category', $category);
            return true;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
            ->viaTable('article_tag', ['article_id' => 'id']);
    }

    /**
     * @return array
     */
    public function getSelectedTags()
    {
        $selectedIds = $this
            ->getTags()
            ->select('id')
            ->asArray()
            ->all();

        return ArrayHelper::getColumn($selectedIds, 'id');
    }

    /**
     * @param  $tags
     * @return $this
     */
    public function saveTags($tags)
    {
        if (is_array($tags)) {
            $this->clearCurrentTags();
            foreach ($tags as $tagId) {
                /** @var ActiveRecordInterface $tag */
                $tag = Tag::findOne($tagId);
                $this->link('tags', $tag);
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function clearCurrentTags()
    {
        ArticleTag::deleteAll(['article_id' => $this->id]);

        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return Yii::$app->formatter->asDate($this->date);
    }

    /**
     * @param  int $pageSize
     * @return array
     */
    public static function getAll($pageSize = self::ARTICLES_PER_PAGE)
    {
        $query = Article::find();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['articles']   = $articles;
        $data['pagination'] = $pagination;

        return $data;
    }

    /**
     * @return \yii\db\ActiveRecord[]
     */
    public static function getPopular()
    {
        return Article::find()
                ->orderBy('viewed desc')
                ->limit(3)
                ->all();
    }

    /**
     * @return \yii\db\ActiveRecord[]
     */
    public static function getRecent()
    {
        return Article::find()
            ->orderBy('date asc')
            ->limit(4)
            ->all();
    }

    /**
     * @return bool
     */
    public function saveArticle()
    {
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::class, ['article_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveRecord[]
     */
    public function getArticleComments()
    {
        return $this->getComments()->where(['status' => 1])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAuthor()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }

    /**
     * @return bool
     */
    public function viewedCounter()
    {
        $this->viewed += 1;

        return $this->save(false);
    }
}
