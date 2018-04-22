<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;

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
     *
     */
    public function deleteImage()
    {
        /** @var ImageUpload $imageUploadModel */
        $imageUploadModel = new ImageUpload();
        $imageUploadModel->deleteCurrentImage($this->image);

        return $this;
    }

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
     * @param  int $category_id
     * @return bool
     */
    public function saveCategory($category_id)
    {
        /** @var ActiveRecordInterface $category */
        $category = Category::findOne($category_id);
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

    public function getDate()
    {
        return Yii::$app->formatter->asDate($this->date);
    }

    public static function getAll($pageSize = 5)
    {
        $query = Article::find();
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $pageSize]);
        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['articles'] = $articles;
        $data['pagination'] = $pagination;

        return $data;
    }

    public static function getPopular()
    {
        return Article::find()
                ->orderBy('viewed desc')
                ->limit(3)
                ->all();
    }

    public static function getRecent()
    {
        return Article::find()
            ->orderBy('date asc')
            ->limit(4)
            ->all();
    }

    public function saveArticle()
    {
        $this->user_id = Yii::$app->user->id;
        return $this->save();
    }

}
