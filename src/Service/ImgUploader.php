<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
        $imgName = \md5(\uniqid('', true)) . '.' . $img->guessExtension();

        try{
            $img->move($this->getTargetDirectory(), $imgName);
        } catch (FileException $e){
            return $e;
        }

        return $imgName;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}
