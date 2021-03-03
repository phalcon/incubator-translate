<?php

/**
 * This file is part of the Phalcon Framework.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

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
    protected $connection;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $language;

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
        array $options = []
    ) {
        parent::__construct($interpolator, $options);

        $this->connection = $connection;
        $this->tableName = $tableName;
        $this->language = $language;
    }

    /**
     * Returns the translation string of the given key
     *
     * @param string $translateKey
     * @param array $placeholders
     * @return string
     */
    public function t(string $translateKey, array $placeholders = []): string
    {
        return $this->query($translateKey, $placeholders);
    }

    /**
     * Returns the translation string of the given key (alias of method 't')
     *
     * @param array $placeholders
     * @param string $translateKey
     * @return string
     */
    public function _(string $translateKey, array $placeholders = []): string
    {
        return $this->t($translateKey, $placeholders);
    }

    /**
     * @param string $translateKey
     * @param array $placeholders
     * @return string
     */
    public function query(string $translateKey, array $placeholders = []): string
    {
        $translation = $this->connection->fetchOne(
            sprintf('SELECT value FROM %s WHERE language = ? AND key_name = ?', $this->tableName),
            Enum::FETCH_ASSOC,
            [$this->language, $translateKey]
        );

        $value = $translation['value'] ?? $translateKey;

        return $this->replacePlaceholders($value, $placeholders);
    }

    /**
     * Check whether a translation key exists
     *
     * @param mixed $translateKey
     * @return bool
     */
    public function offsetExists($translateKey): bool
    {
        return $this->exists($translateKey);
    }

    /**
     * Returns the translation related to the given key
     *
     * @param mixed $translateKey
     * @return mixed
     */
    public function offsetGet($translateKey)
    {
        return $this->query($translateKey);
    }

    /**
     * Sets a translation value
     *
     * @param mixed $offset
     * @param string $value
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->update($offset, $value);
    }

    /**
     * Unsets a translation from the dictionary
     *
     * @param mixed $offset
     * @return void
     */
    public function offsetUnset($offset): void
    {
        $this->connection->delete(
            $this->tableName,
            'key_name = :key AND language = :lang',
            [
                'key'  => $offset,
                'lang' => $this->language,
            ]
        );
    }

    public function exists(string $index): bool
    {
        $result = $this->connection->fetchOne(
            sprintf(
                'SELECT COUNT(*) AS `count` FROM %s WHERE language = :language AND key_name = :key_name',
                $this->tableName
            ),
            Enum::FETCH_ASSOC,
            [
                'language' => $this->language,
                'key_name' => $index,
            ]
        );

        return !empty($result['count']);
    }

    /**
     * Update a translation for given key (No existence check!)
     *
     * @param  string  $translateKey
     * @param  string  $value
     * @return bool
     */
    protected function update(string $translateKey, string $value): bool
    {
        if (!$this->offsetExists($translateKey)) {
            return $this->connection->insertAsDict(
                $this->tableName,
                [
                    'key_name' => $translateKey,
                    'language' => $this->language,
                    'value' => $value,
                ]
            );
        }

        return $this->connection->updateAsDict(
            $this->tableName,
            ['value' => $value],
            [
                'conditions' => 'key_name = ? AND language = ?',
                'bind' => [
                    'key'  => $translateKey,
                    'lang' => $this->language,
                ]
            ]
        );
    }
}
