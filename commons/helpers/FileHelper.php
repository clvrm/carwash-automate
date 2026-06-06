<?php


namespace app\commons\helpers;


use yii\base\Exception;
use \yii\helpers\FileHelper as FH;
use yii\web\UploadedFile;

/**
 * Class FileHelper
 * @package app\commons\helpers
 */
class FileHelper
{
    private const UPLOAD_DIRECTORY_NAME = '/uploads';

    /**
     * @param UploadedFile $uploadedFile
     * @param $path
     * @param false $randomizeName
     * @return string|false
     */
    public static function saveUploaded(UploadedFile $uploadedFile, $path, $randomizeName = false)
    {
        try {
            $name = $uploadedFile->getBaseName();
            if ($randomizeName) {
                $name = md5(random_int(1, 999999999));
            }
            $fullPath = $path . $name . '.' . $uploadedFile->getExtension();
            if (self::checkPath($path, true) && $uploadedFile->saveAs($fullPath)) {
                return substr($fullPath, strpos($fullPath, self::UPLOAD_DIRECTORY_NAME));
            }
        } catch (\Exception $exception) {
            \Yii::error('Не удалось загрузить файл: ' . $exception->getMessage());
        }
        return false;
    }

    /**
     * @param $path
     * @param false $createIfNotExists
     * @return bool
     * @throws Exception
     */
    private static function checkPath($path, $createIfNotExists = false): bool
    {
        if (is_dir($path)) {
            return true;
        }

        if ($createIfNotExists) {
            return self::createDirectory($path);
        }

        return false;
    }

    /**
     * @param $path
     * @param int $mode
     * @param bool $recursive
     * @return bool
     * @throws Exception
     */
    private static function createDirectory($path, $mode = 0755, $recursive = true): bool
    {
        return FH::createDirectory($path, $mode, $recursive);
    }
}