<?php


namespace App\Service;


use App\Entity\Product;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;

class UploadService
{
    /** @var KernelInterface */
    private $kernel;

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    function saveImage(UploadedFile $image, $dir)
    {
        $uploadDirectory = 'uploads/images/'.$dir;
        $path = $this->kernel->getProjectDir().'/public/' . $uploadDirectory;
        $imageName = uniqid() . '.' . $image->guessExtension();
        $image->move($path, $imageName);
        return '/'. $uploadDirectory. '/' . $imageName;
    }

    function deleteImage($productId)
    {
        $uploadDirectory = 'uploads/images/' . $productId;
        $path = $this->kernel->getProjectDir().'/public/' . $uploadDirectory;

        if (file_exists($path)) {
            $fileSystem = new Filesystem();

            $fileSystem->remove($path);
            return true;
        }

        return false;
    }
}
