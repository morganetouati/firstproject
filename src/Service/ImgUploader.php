<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImgUploader
{
    private $targetDirectory;

    /**
     * @param $targetDirectory
     */
    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $img)
    {
        $imgName = md5(uniqid('', true)) . '.' . $img->guessExtension();
        $img->move($this->targetDirectory, $imgName);
        return $imgName;
    }
}