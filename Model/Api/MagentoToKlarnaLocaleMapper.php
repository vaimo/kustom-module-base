<?php
/**
 * Copyright © Klarna Bank AB (publ)
 *
 * For the full copyright and license information, please view the NOTICE
 * and LICENSE files that were distributed with this source code.
 */
declare(strict_types=1);

namespace Klarna\Base\Model\Api;

use Magento\Store\Api\Data\StoreInterface;
use Magento\Framework\Locale\ResolverInterface as LocaleResolverInterface;

/**
 * @internal
 */
class MagentoToKlarnaLocaleMapper
{
    /**
     * @var LocaleResolverInterface
     */
    private LocaleResolverInterface $localeResolver;

    /**
     * @param LocaleResolverInterface $localeResolver
     * @codeCoverageIgnore
     */
    public function __construct(LocaleResolverInterface $localeResolver)
    {
        $this->localeResolver = $localeResolver;
    }

    /**
     * Map Magento supported locales (BPC 47) with Klarna supported ones (RFC1766)
     *
     * @param StoreInterface $store
     * @return string
     */
    public function getLocale(StoreInterface $store): string
    {
        $localesToConvert = [
            'nb_no' => 'nb_NO',
        ];

        $locale = $store->getLocale();
        if ($locale === null) {
            $locale = $this->localeResolver->getLocale();
        }
        return str_replace('_', '-', $localesToConvert[strtolower($locale)] ?? $locale);
    }

    /**
     * @inheritDoc
     */
    public function setLocale($locale = null): self
    {
        $this->localeResolver->setLocale($locale);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function setDefaultLocale($locale): self
    {
        $this->localeResolver->setDefaultLocale($locale);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getDefaultLocale(): string
    {
        return $this->localeResolver->getDefaultLocale();
    }

    /**
     * @inheritDoc
     */
    public function emulate($scopeId): ?string
    {
        return $this->localeResolver->emulate($scopeId);
    }

    /**
     * @inheritDoc
     */
    public function revert(): ?string
    {
        return $this->localeResolver->revert();
    }

    /**
     * @inheritDoc
     */
    public function getDefaultLocalePath()
    {
        return $this->localeResolver->getDefaultLocalePath();
    }
}
