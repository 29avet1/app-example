<?php

namespace App\Traits;

use Exception;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait FileUpload
{
    /**
     * @param UploadedFile $file
     * @param string       $filePath
     * @return array
     */
    protected function uploadFile(UploadedFile $file, string $filePath): array
    {
        $filePath = trim($filePath, '/');
        $fileOriginalName = $file->getClientOriginalName();
        $fileOriginalName = ($fileOriginalName == 'blob') ? 'audio-' . str_random(10) . '.wav' : $fileOriginalName;
        $fileMimeType = $file->getClientMimeType();
        $fileNameInfo = pathinfo($fileOriginalName);
        $fileHashName = str_slug($fileNameInfo['filename']) . '-' . str_random(10) . '.' . $fileNameInfo['extension'];//$file->hashName();

        if (
            (format_from_mime($fileMimeType) === 'image') &&
            ($fileMimeType !== 'image/gif') &&
            ($file->getSize() > 400000)
        ) {
            $file = $this->formatImage($file, 1000);
        } else {
            $file = $file->get();
        }

        Storage::disk('s3_public')->put($filePath . '/' . $fileHashName, $file);
        $url = config('filesystems.disks.s3_public.domain') . '/' . $filePath . '/' . $fileHashName;

        return [
            'url'           => $url,
            'original_name' => $fileOriginalName,
            'hash_name'     => $fileHashName,
            'mime_type'     => $fileMimeType,
        ];
    }

    /**
     * @param Stream $file
     * @param array  $fileData
     * @param string $filePath
     * @return array
     */
    protected function uploadGuzzleStream(Stream $file, array $fileData, string $filePath): array
    {
        $filePath = trim($filePath, '/');
        $fileOriginalName = $fileData['filename'];
        $fileMimeType = $fileData['content_type'];
        $fileNameInfo = pathinfo($fileOriginalName);
        $fileHashName = str_slug($fileNameInfo['filename']) . '-' . str_random(10) . '.' . $fileNameInfo['extension'];//$file->hashName();

        Storage::disk('s3_public')->put($filePath . '/' . $fileHashName, $file->getContents());
        $url = config('filesystems.disks.s3_public.domain') . '/' . $filePath . '/' . $fileHashName;

        return [
            'url'           => $url,
            'original_name' => $fileOriginalName,
            'hash_name'     => $fileHashName,
            'mime_type'     => $fileMimeType,
        ];
    }

    /**
     * @param UploadedFile $file
     * @param string       $filePath
     * @param int          $size
     * @param bool         $convert
     * @return array
     */
    protected function uploadImage(UploadedFile $file, string $filePath, $size = 400, $convert = true): array
    {
        $filePath = trim($filePath, '/');
        $fileOriginalName = $file->getClientOriginalName();
        $fileMimeType = $file->getMimeType();
        $fileHashName = $file->hashName();

        if ($convert) {
            $file = $this->formatImage($file, $size);
        }

        Storage::disk('s3_public')->put($convert ? $filePath . '/' . $fileHashName : $filePath, $file);
        $url = config('filesystems.disks.s3_public.domain') . '/' . $filePath . '/' . $fileHashName;

        return [
            'url'           => $url,
            'original_name' => $fileOriginalName,
            'hash_name'     => $fileHashName,
            'mime_type'     => $fileMimeType,
        ];
    }

    /**
     * Resize an image instance for the given file.
     *
     * @param     $image
     * @param int $size
     * @return \Intervention\Image\Image|string
     */
    protected function formatImage($image, $size = 400)
    {
        list($imageWidth, $imageHeight) = getimagesize($image);

        if ($imageWidth >= $imageHeight) {
            $thumbWidth = $size;
            $thumbHeight = null;
        } else {
            $thumbHeight = $size;
            $thumbWidth = null;
        }

        return (string)Image::make($image)
            ->resize($thumbWidth, $thumbHeight, function ($constraint) {
                $constraint->aspectRatio();
            })->encode();
    }

    /**
     * @param string $fromPath
     * @param string $toPath
     * @param string $fileName
     * @param bool   $copy
     * @return null|string
     */
    protected function moveFile(string $fromPath, string $toPath, string $fileName, $copy = false): ?string
    {
        $oldPath = trim($fromPath, '/') . '/' . $fileName;
        $newPath = trim($toPath, '/') . '/' . $fileName;

        try {
            if ($copy) {
                Storage::disk('s3_public')->copy($oldPath, $newPath);
            } else {
                Storage::disk('s3_public')->move($oldPath, $newPath);
            }

            return config('filesystems.disks.s3_public.domain') . '/' . $newPath;
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param string $filePath
     * @return bool
     */
    protected function removeFile(string $filePath): bool
    {
        return Storage::disk('s3_public')->delete($filePath);
    }
}