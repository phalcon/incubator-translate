<?php

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Interpolator;

use MessageFormatter;
use Phalcon\Translate\Exception;
use Phalcon\Translate\Interpolator\InterpolatorInterface;

class Intl implements InterpolatorInterface
{
    /**
     * @var string
     */
    private $locale;

    /**
     * Intl constructor.
     *
     * @param string $locale
     */
    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    /**
     * Replaces placeholders by the values passed
     * Use the MessageFormatter class.
     *
     * @see http://php.net/manual/en/class.messageformatter.php
     * @param string $translation
     * @param array $placeholders
     * @return string
     * @throws Exception
     */
    public function replacePlaceholders(string $translation, array $placeholders = []): string
    {
        if (empty($placeholders)) {
            return $translation;
        }

        $fmt = new MessageFormatter($this->locale, $translation);
        $translation = $fmt->format($placeholders);
        if ($translation === false) {
            throw new Exception($fmt->getErrorMessage(), $fmt->getErrorCode());
        }

        return $translation;
    }
}
