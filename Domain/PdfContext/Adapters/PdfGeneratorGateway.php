<?php

declare(strict_types=1);

namespace Domain\PdfContext\Adapters;

use Psr\Http\Message\StreamInterface;

interface PdfGeneratorGateway
{
    public function generatePdf(string $content, ?string $footer): StreamInterface;
}
