<?php

namespace Philly\Http;

use DateTime;
use InvalidArgumentException;
use Philly\Collection\ArrayList;
use Philly\Collection\ArrayMap;
use Philly\Collection\KeyValuePair;
use Philly\Support\ToStringCompatible;

class Cookie implements Contract\Cookie
{
	use ToStringCompatible;

	/**
	 * @param string $cookies
	 * @return ArrayList<\Philly\Http\Contract\Cookie>
	 */
	public static function fromClientString(string $cookies): ArrayList
	{
		return ArrayList::fromArray(mb_split(';', $cookies))
			->map(static function (mixed $cookie): Contract\Cookie {
				if (!is_string($cookie)) {
					throw new InvalidArgumentException('Cookie must be a string');
				}

				[$name, $value] = explode('=', $cookie, 2);

				/** @var Contract\Cookie */
				return new self($name, $value);
			});
	}

	/**
	 * @param string $cookie
	 * @return Philly\Http\Contract\Cookie
	 */
	public static function fromResponseString(string $cookieString): Contract\Cookie
	{
		$propertyMap = ArrayMap::fromKeyValuePairList(ArrayList::fromArray(mb_split(';', $cookieString))
			->map(static function (mixed $keyValue): Contract\Cookie {
				if (!is_string($keyValue)) {
					throw new InvalidArgumentException('Cookie must be a string');
				}

				[$key, $value] = explode('=', trim($keyValue), 2);

				return new KeyValuePair(mb_strtolower($key), $value);
			}));

		if (!$propertyMap->has('name')) {
			throw new InvalidArgumentException('Cookie must have a name');
		}

		$cookie = new self($propertyMap->get('name'));

		if ($propertyMap->has('value'))

		return $cookie;
	}

	private string $name;

	private ?string $value;

	private ?DateTime $expires;

	private ?string $path;

	private ?string $domain;

	private bool $secure;

	private bool $httpOnly;

	private ?CookieSameSite $sameSite;

	public ?int $maxAge;

	public function __construct(
		string          $name,
		?string         $value = null,
		?DateTime       $expires = null,
		?string         $path = null,
		?string         $domain = null,
		bool            $secure = false,
		bool            $httpOnly = false,
		?CookieSameSite $sameSite = null,
		?int            $maxAge = null
	)
	{
		$this->name = $name;
		$this->value = $value;
		$this->expires = $expires;
		$this->path = $path;
		$this->domain = $domain;
		$this->secure = $secure;
		$this->httpOnly = $httpOnly;
		$this->sameSite = $sameSite;
		$this->maxAge = $maxAge;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getValue(): ?string
	{
		return $this->value;
	}

	public function setValue(?string $value): void
	{
		$this->value = $value;
	}

	public function getExpires(): ?DateTime
	{
		return $this->expires;
	}

	public function setExpires(?DateTime $expires): void
	{
		$this->expires = $expires;
	}

	public function getPath(): ?string
	{
		return $this->path;
	}

	public function setPath(?string $path): void
	{
		$this->path = $path;
	}

	public function getDomain(): ?string
	{
		return $this->domain;
	}

	public function setDomain(?string $domain): void
	{
		$this->domain = $domain;
	}

	public function isSecure(): bool
	{
		return $this->secure;
	}

	public function setSecure(bool $secure): void
	{
		$this->secure = $secure;
	}

	public function isHttpOnly(): bool
	{
		return $this->httpOnly;
	}

	public function setHttpOnly(bool $httpOnly): void
	{
		$this->httpOnly = $httpOnly;
	}

	public function getSameSite(): ?CookieSameSite
	{
		return $this->sameSite;
	}

	public function setSameSite(?CookieSameSite $sameSite): void
	{
		$this->sameSite = $sameSite;
	}

	public function getMaxAge(): ?int
	{
		return $this->maxAge;
	}

	public function setMaxAge(?int $maxAge): void
	{
		$this->maxAge = $maxAge;
	}

	public function asString(): string
	{
		$cookie = $this->name . '=' . ($this->value ?? '');

		if ($this->expires) {
			$cookie .= '; Expires=' . $this->expires->format('D, d-M-Y H:i:s T');
		}

		if ($this->path) {
			$cookie .= '; Path=' . $this->path;
		}

		if ($this->domain) {
			$cookie .= '; Domain=' . $this->domain;
		}

		if ($this->secure) {
			$cookie .= '; Secure';
		}

		if ($this->httpOnly) {
			$cookie .= '; HttpOnly';
		}

		if ($this->sameSite) {
			/**
			 * @var string $sameSite
			 * @psalm-suppress UndefinedPropertyFetch Until vimeo/psalm#6468 is fixed
			 */
			$sameSite = $this->sameSite->value;

			$cookie .= '; SameSite=' . $sameSite;
		}

		if ($this->maxAge) {
			$cookie .= '; Max-Age=' . $this->maxAge;
		}

		return $cookie;
	}
}
