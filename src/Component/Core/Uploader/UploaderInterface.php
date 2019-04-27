<?php


namespace App\Component\Core\Uploader;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

interface UploaderInterface
{
    /**
     * @param UploadedFile $file
     *
     * @return File
     */
    public function upload(UploadedFile $file): File ;
}