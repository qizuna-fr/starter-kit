<?php

declare(strict_types=1);

/** Qizuna 2024 - tous droits reservés  **/

namespace Infrastructure\Service\S3;


use Aws\Exception\AwsException;
use Aws\S3\S3ClientInterface;

class S3Service
{
    private $s3Client;
    private $bucket;

    public function __construct(S3ClientInterface $s3Client, string $bucket)
    {
        $this->s3Client = $s3Client;
        $this->bucket = $bucket;
    }

    public function listFiles(): array
    {
        try {
            $result = $this->s3Client->listObjects(
                [
                    'Bucket' => $this->bucket,
                ]
            );

            return $result['Contents'] ?? [];
        } catch (AwsException $e) {
            throw new \Exception('Unable to list objects: ' . $e->getMessage());
        }
    }

    public function generateDownloadUrls(): array
    {
        $files = $this->listFiles();
        $urls = [];

        foreach ($files as $file) {
            $urls[] = [
                'filename' => $file['Key'],
                'url' => $this->generatePresignedUrl($file['Key']),
                'extension' => pathinfo($file['Key'], PATHINFO_EXTENSION),
            ];
        }

        return $urls;
    }

    private function generatePresignedUrl(string $key): string
    {
        $cmd = $this->s3Client->getCommand('GetObject', [
            'Bucket' => $this->bucket,
            'Key' => $key,
        ]);

        $request = $this->s3Client->createPresignedRequest($cmd, '+20 minutes');
        return (string)$request->getUri();
    }
}
