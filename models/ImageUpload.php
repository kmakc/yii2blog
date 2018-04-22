<?php
namespace app\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class ImageUpload extends Model
{
    /**
     * @var UploadedFile $image
     */
    public $image;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [['image'], 'required'],
            [['image'], 'file', 'extensions' => 'jpg,png']
        ];
    }

    /**
     * @param  UploadedFile $file
     * @param  string       $currentImage
     * @return string
     */
    public function uploadFile(UploadedFile $file, $currentImage)
    {
        $this->image = $file;

        if ($this->validate()) {
            $this->deleteCurrentImage($currentImage);
            return $this->saveImage();
        }

        return false;
    }

    /**
     * @return string
     */
    private function getFolder()
    {
        return Yii::getAlias('@web'). 'uploads/';
    }

    /**
     * @return string
     */
    private function generateFileName()
    {
        return strtolower(md5(uniqid($this->image->baseName)) . '.' . $this->image->extension);
    }

    /**
     * @param  string $currentImage
     * @return bool
     */
    public function deleteCurrentImage($currentImage)
    {
        if ($this->isFileExists($currentImage)) {
            unlink($this->getFolder() . $currentImage);
            return true;
        }

        return false;
    }

    /**
     * @param  string $file
     * @return bool
     */
    public function isFileExists($file)
    {
        if (!empty($file) && $file != null) {
            return file_exists($this->getFolder() . $file);
        }

        return false;
    }

    /**
     * @return string
     */
    private function saveImage()
    {
        $fileName = $this->generateFileName();

        $this->image->saveAs($this->getFolder() . $fileName);
        return $fileName;
    }
}
