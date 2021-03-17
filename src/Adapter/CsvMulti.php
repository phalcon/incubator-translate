<?php

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Adapter;

use Phalcon\Translate\Adapter\AbstractAdapter;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\Exception;
use Phalcon\Translate\InterpolatorFactory;

class CsvMulti extends AbstractAdapter implements AdapterInterface, \ArrayAccess
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
     * @var array
     */
    private $indexes = [];

    /**
     * @var array
     */
    private $translate;

    /**
     * CsvMulti constructor.
     *
     * @param string              $content
     * @param string              $locale
     * @param InterpolatorFactory $interpolator
     * @param array               $options
     *
     * @throws Exception
     */
    public function __construct(
        string $content,
        string $locale,
        InterpolatorFactory $interpolator,
        array $options = []
    ) {
        parent::__construct($interpolator, $options);

        $delimiter = $options["delimiter"] ?? ";";
        $enclosure = $options["enclosure"] ?? "\"";

        $this->load($content, 0, $delimiter, $enclosure);
        $this->setLocale($locale);
    }

    /**
     * Returns the translation string of the given key
     *
     * @param string $translateKey
     * @param array  $placeholders
     *
     * @return string
     * @throws Exception
     */
    public function t(string $translateKey, array $placeholders = []): string
    {
        return $this->query($translateKey, $placeholders);
    }

    /**
     * Returns the translation string of the given key (alias of method 't')
     *
     * @param array  $placeholders
     * @param string $translateKey
     *
     * @return string
     * @throws Exception
     */
    public function _(string $translateKey, array $placeholders = []): string
    {
        return $this->t($translateKey, $placeholders);
    }

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
            return $index;
        }

        $translation = $this->locale ? $this->translate[$this->locale][$index] : $index;

        return $this->replacePlaceholders($translation, $placeholders);
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


    /**
     * Check whether a translation key exists
     *
     * @param mixed $translateKey
     *
     * @return bool
     * @throws Exception
     */
    public function offsetExists($translateKey): bool
    {
        return $this->exists($translateKey);
    }

    /**
     * Returns the translation related to the given key
     *
     * @param mixed $translateKey
     *
     * @return mixed
     * @throws Exception
     */
    public function offsetGet($translateKey)
    {
        return $this->query($translateKey);
    }

    /**
     * Sets a translation value
     *
     * @param mixed  $offset
     * @param string $value
     *
     * @return void
     * @throws Exception
     */
    public function offsetSet($offset, $value): void
    {
        throw new Exception("Translate is an immutable ArrayAccess object");
    }

    /**
     * Load translates from file
     *
     * @param string $file
     * @param int $length
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
}
