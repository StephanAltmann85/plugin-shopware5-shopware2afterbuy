<?php

namespace FatchipAfterbuy\ValueObjects;

class ProductPicture
{
    /** @var string Afterbuy internal number 1 - 6 */
    private $nr;

    /** @var string */
    private $url;

    /** @var string */
    private $altText = '';

    /**
     * Afterbuy internal number 1 - 6
     *
     * @return string
     */
    public function getNr(): string
    {
        return $this->nr;
    }

    /**
     * Afterbuy internal number 1 - 6
     *
     * @param string $nr
     */
    public function setNr(string $nr): void
    {
        $this->nr = $nr;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @param string $url
     */
    public function setUrl(string $url): void
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getAltText(): string
    {
        return $this->altText;
    }

    /**
     * @param string $altText
     */
    public function setAltText(?string $altText): void
    {
        if ($altText === null) {
            $altText = '';
        }
        $this->altText = $altText;
    }
}
