<?php

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Adapter;

use Phalcon\Db\Enum;
use Phalcon\Translate\Adapter\AbstractAdapter;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Db\Adapter\AbstractAdapter as DbAbstractAdapter;
use Phalcon\Translate\InterpolatorFactory;

class Database extends AbstractAdapter implements AdapterInterface
{
    /**
     * @var DbAbstractAdapter
     */
    private $connection;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $language;

    /**
     * Database constructor.
     *
     * @param DbAbstractAdapter $connection
     * @param string $tableName
     * @param string $language
     * @param InterpolatorFactory $interpolator
     * @param array $options
     */
    public function __construct(
        DbAbstractAdapter $connection,
        string $tableName,
        string $language,
        InterpolatorFactory $interpolator,
        array $options
    ) {
        parent::__construct($interpolator, $options);

        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->language = $language;
    }

    /**
     * Returns the translation string of the given key (alias of method 't')
     *
     * @param array $placeholders
     * @param string $translateKey
     * @return string
     */
    public function _(string $translateKey, array $placeholders = array()): string
    {
    }

    /**
     * Check whether a translation key exists
     *
     * @param mixed $translateKey
     * @return bool
     */
    public function offsetExists($translateKey): bool
    {
    }

    /**
     * Returns the translation related to the given key
     *
     * @param mixed $translateKey
     * @return mixed
     */
    public function offsetGet($translateKey)
    {
    }

    /**
     * Sets a translation value
     *
     * @param string $value
     * @param mixed $offset
     * @return void
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * Unsets a translation from the dictionary
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset)
    {
    }

    public function exists(string $index): bool
    {
        // TODO: Implement exists() method.
    }

    public function query(string $translateKey, array $placeholders = array()): string
    {
        $translation = $this->connection->fetchOne(
            sprintf('SELECT value FROM %s WHERE language = ? AND key_name = ?', $this->tableName),
            Enum::FETCH_ASSOC,
            [$this->language, $translateKey]
        );

        $value = $translation['value'] ?? $translateKey;

        return $this->replacePlaceholders($value, $placeholders);
    }

    public function t(string $translateKey, array $placeholders = array()): string
    {
        // TODO: Implement t() method.
    }
}
