<?php


namespace App\Component\Core\Uploader;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader implements UploaderInterface
{
    /**
     * @var string
     */
    protected $targetDir;

    /**
     * @var string
     */
    protected $fileName;

    /**
     * FileUploader constructor.
     *
     * @param $targetDir
     */
    public function __construct($targetDir)
    {
        $this->targetDir = $targetDir;
    }

    public function upload(UploadedFile $file): File
    {
        $fileUploadName = $this->generateFileName($file);

        return $file->move($this->getTargetDir(), $fileUploadName);
    }

    /**
     * @param UploadedFile $file
     *
     * @return string
     */
    private function generateFileName(UploadedFile $file): string
    {
        $extGuesser = (new MimeTypeExtensionGuesser())->guess($file->getClientMimeType());
        $fileName = !empty($this->fileName)
            ? $this->fileName
            : str_replace('.' . $extGuesser, '', $file->getClientOriginalName());

        return $fileName . '.' . $extGuesser;
    }

    /**
     * @param string $targetDir
     *
     * @return $this
     */
    public function setTargetDir(string $targetDir): self
    {
        $this->targetDir = $targetDir;

        return $this;
    }

    /**
     * @return string
     */
    public function getTargetDir(): string
    {
        return $this->targetDir;
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return $this->fileName;
    }
}
