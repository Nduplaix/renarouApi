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

    function deleteProductImages($productId)
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

    function deleteImage($path)
    {
        if (file_exists($path)) {
            $fileSystem = new Filesystem();

            $fileSystem->remove($path);
            return true;
        }

        return false;
    }

    function moveImage($oldPath, $productId)
    {
        $basePath = $this->kernel->getProjectDir().'/public/';
        if (file_exists($basePath . $oldPath)) {
            $fileSystem = new Filesystem();

            $imagePath = explode("/", $oldPath);

            $image = end($imagePath);

            $uploadDirectory = 'uploads/images/' . $productId . "/" . $image;
            $path = $this->kernel->getProjectDir().'/public/' . $uploadDirectory;

            $fileSystem->copy($basePath . $oldPath, $path);

            if ($basePath . $oldPath !== $path) {
                $fileSystem->remove($basePath . $oldPath);
            }

            return '/' . $uploadDirectory;
        }

        return false;
    }

}
