<?php declare(strict_types = 1);

/**
 * Test: Contributte\PdfResponse\PdfResponse and page format.
 *
 * @httpCode -
 */

use Contributte\PdfResponse\InvalidStateException;
use Contributte\PdfResponse\PdfResponse;
use Nette\Http\Request;
use Nette\Http\Response;
use Nette\Http\UrlScript;
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
$origData = file_get_contents(__DIR__ . '/templates/example1.htm');

test(function () use ($origData): void {
	$fileResponse = new PdfResponse($origData);
	$fileResponse->setSaveMode(PdfResponse::INLINE);
	$fileResponse->pageOrientation = PdfResponse::ORIENTATION_LANDSCAPE;
	$fileResponse->pageFormat = 'A4-L';
	$fileResponse->pageMargins = $fileResponse->getPageMargins();

	ob_start();
	$fileResponse->send(new Request(new UrlScript()), new Response());
	$actualData = ob_get_clean();

	Assert::match('#^%PDF-#i', $actualData);
});

test(function () use ($origData): void {
	$fileResponse = new PdfResponse($origData);
	$fileResponse->getMPDF();

	Assert::exception(
		function () use ($fileResponse): void {
			$fileResponse->pageOrientation = PdfResponse::ORIENTATION_LANDSCAPE;
		},
		InvalidStateException::class,
		'mPDF instance already created. Set page orientation before calling getMPDF'
	);
});

test(function () use ($origData): void {
	$fileResponse = new PdfResponse($origData);
	$fileResponse->getMPDF();

	Assert::exception(
		function () use ($fileResponse): void {
			$fileResponse->pageFormat = 'A4-L';
		},
		InvalidStateException::class,
		'mPDF instance already created. Set page format before calling getMPDF'
	);
});

test(function () use ($origData): void {
	$fileResponse = new PdfResponse($origData);
	$fileResponse->getMPDF();

	Assert::exception(
		function () use ($fileResponse): void {
			$fileResponse->pageMargins = $fileResponse->getPageMargins();
		},
		InvalidStateException::class,
		'mPDF instance already created. Set page margins before calling getMPDF'
	);
});
