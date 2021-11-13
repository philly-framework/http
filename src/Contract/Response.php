<?php
declare(strict_types=1);

namespace Elephox\Http\Contract;

use Elephox\Support\Contract\MimeType as MimeTypeContract;

interface Response
{
	public function getHeaders(): ResponseHeaderMap;

	public function setCode(ResponseCode $code): void;

	public function getCode(): ResponseCode;

	public function setContent(string $content, ?MimeTypeContract $mimeType = null): void;

	public function getContent(): string;

	public function getHttpVersion(): string;

	public function send(): void;
}
