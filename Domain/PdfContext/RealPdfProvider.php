<?php

declare(strict_types=1);

namespace Domain\PdfContext;

use Domain\PdfContext\Adapters\PdfGeneratorGateway;
use Gotenberg\Gotenberg;
use Gotenberg\Stream;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\StreamInterface;

final class RealPdfProvider implements PdfGeneratorGateway
{
    private string $gotenbergUrl;
    private ClientInterface $httpClient;

    public function __construct(
        string $gotenbergUrl,
        ClientInterface $httpClient,
    ) {
        $this->gotenbergUrl = $gotenbergUrl;
        $this->httpClient = $httpClient;
    }

    public function generatePdf(string $content, ?string $footer): StreamInterface
    {
        $request = Gotenberg::chromium($this->gotenbergUrl)
            //->margins(0.0, 1, 0 , 0)
            ->emulatePrintMediaType()
            ->preferCssPageSize()
            //->paperSize(8.27 ,  9.7)
            ->printBackground()
            ->omitBackground()
            ->waitDelay("1s")
            ->formValue('Roboto', 'https://fonts.googleapis.com/css2?family=Roboto:wght@300&display=swap')
            //->footer(Stream::string('footer.html', $footer))
            ->html(Stream::string(
                'my.pdf',
                $content
            ));

        $response = Gotenberg::send($request, $this->httpClient);

        return $response->getBody();
    }
}
