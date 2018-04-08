<?php

namespace app\models;

use Yii;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "category".
 *
 * @property int $id
 * @property string $title
 */
class Category extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'title' => 'Title',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArticles()
    {
        return $this->hasMany(Article::class, ['category_id' => 'id']);
    }

    /**
     * @return array
     */
    public static function getCategoriesList()
    {
        return ArrayHelper::map(Category::find()->all(), 'id', 'title');
    }

    public function getArticlesCount()
    {
        return $this->getArticles()->count();
    }

    public static function getAll()
    {
        return Category::find()->all();
    }

    public static function getArticlesByCategory($id)
    {
        $query = Article::find()->where(['category_id' => $id]);
        $count = $query->count();
        $pagination = new Pagination(['totalCount' => $count, 'pageSize' => 6]);
        $articles = $query
            ->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $data['articles'] = $articles;
        $data['pagination'] = $pagination;

        return $data;
    }
}
