<?php

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    public function uploadImage(UploadedFile $file, string $folder = 'marketnest'): array
    {
        $result = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder,
            'transformation' => [
                'quality' => 'auto',
                'fetch_format' => 'auto',
            ],
        ]);

        return [
            'url' => $result->getSecurePath(),
            'public_id' => $result->getPublicId(),
        ];
    }

    public function uploadMultiple(array $files, string $folder = 'marketnest'): array
    {
        $results = [];
        foreach ($files as $file) {
            $results[] = $this->uploadImage($file, $folder)['url'];
        }
        return $results;
    }

    public function deleteImage(string $publicId): void
    {
        Cloudinary::destroy($publicId);
    }
}
