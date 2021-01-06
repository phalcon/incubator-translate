<?php

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Adapter;

use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\Adapter\Csv;
use Phalcon\Translate\Exception;

class CsvMulti extends Csv implements AdapterInterface
{
    /**
     * @var array
     */
    private $locales = [];

    /**
     * @var string|null
     */
    private $locale = null;

    /**
     * @var string
     */
    private $indexes = [];

    /**
     * Check whether is defined a translation key in the internal array
     *
     * @param string $index
     * @return bool
     * @throws Exception
     */
    public function exists(string $index): bool
    {
        if (is_null($this->locale)) {
            throw new Exception('The locale must have been defined.');
        }

        return in_array($index, $this->getIndexes());
    }

    /**
     * Returns the translation related to the given key and the previously set locale
     *
     * @param string $index
     * @param array $placeholders
     * @return string
     * @throws Exception
     */
    public function query(string $index, array $placeholders = []): string
    {
        if (!$this->exists($index)) {
            throw new Exception("They key '{$index}' was not found.");
        }

        if ($this->locale === false) {
            // "no translation mode"
            $translation = $index;
        } else {
            $translation = $this->translate[$this->locale][$index];
        }

        return $this->replacePlaceholders($translation, $placeholders);
    }

    /**
     * Load translates from file
     *
     * @param string $file
     * @param int length
     * @param string $delimiter
     * @param string $enclosure
     * @throws Exception
     */
    private function load(string $file, int $length, string $delimiter, string $enclosure)
    {
        $fileHandler = fopen($file, 'rb');
        if ($fileHandler === false) {
            throw new Exception("Error opening translation file '" . $file . "'");
        }

        $line = 0;
        while ($data = fgetcsv($fileHandler, $length, $delimiter, $enclosure)) {
            if ($line++ == 0) {
                /**
                 * First csv line register
                 * the horizontal locales sort order
                 * the first element (must be empty) is removed
                 */
                foreach (array_slice($data, 1) as $pos => $locale) {
                    $this->locales[$pos] = $locale;
                }

                continue;
            }

            // The first row is the translation index (label)
            $index = array_shift($data);
            // Store this index internally
            $this->indexes[] = $index;

            // The first element is removed as well, so the pos is according to the first line
            foreach ($data as $pos => $translation) {
                $this->translate[$this->locales[$pos]][$index] = $translation;
            }
        }

        fclose($fileHandler);
    }

    /**
     * Sets locale information, according to one from the header row of the source csv
     * Set it to false for enabling the "no translation mode"
     *
     * <code>
     * // Set locale to Dutch
     * $adapter->setLocale('nl_NL');
     * </code>
     *
     * @param string $locale
     * @return CsvMulti
     * @throws Exception
     */
    public function setLocale(string $locale): self
    {
        if (!array_key_exists($locale, $this->translate)) {
            throw new Exception("The locale '{$locale}' is not available in the data source.");
        }

        $this->locale = $locale;

        return $this;
    }

    /**
     * Returns all the translation keys
     */
    public function getIndexes(): array
    {
        return $this->indexes;
    }
}