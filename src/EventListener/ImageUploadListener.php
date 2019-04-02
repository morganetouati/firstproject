<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\BlogPost;
use App\Service\ImgUploader;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageUploadListener
{
    private $uploader;

    public function __construct(ImgUploader $uploader)
    {
        $this->uploader = $uploader;
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $entity = $args->getEntity();
        $this->uploadFile($entity);
    }

    private function uploadFile($entity): void
    {
        if (!$entity instanceof BlogPost) {
            return;
        }
        $img = $entity->getImgUploaded();

        if ($img instanceof UploadedFile) {
            $imgName = $this->uploader->upload($img);
            $entity->setImgUploaded($imgName);
        } elseif ($img instanceof File) {
            $entity->setImgUploaded($img->getFilename());
        }
    }

    public function postLoad(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof BlogPost) {
            return;
        }

        if ($imgName = $entity->getImgUploaded()) {
            $entity->setImgUploaded(new File($this->uploader->getTargetDirectory() . '/' . $imgName));
        }
    }
}
