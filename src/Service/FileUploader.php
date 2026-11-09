<?php

namespace App\Service;

use App\Entity\Product;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    public function __construct(
        #[Autowire('%kernel.project_dir%/public/'.ProductImage::DIRECTORY)]
        private string $targetDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    /** @throws FileException */
    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $this->slugger->slug($originalFilename).'-'.uniqid().'.'.$file->guessExtension();

        $file->move($this->targetDirectory, $fileName);

        return $fileName;
    }

    /**
     * @param UploadedFile[] $files
     *
     * @throws FileException
     */
    public function addProductImages(Product $product, array $files): void
    {
        $filenames = $product->getImages();
        foreach ($files as $file) {
            $filenames[] = $this->upload($file);
        }
        $product->setImages($filenames);
    }
}
