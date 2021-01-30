<?php

declare(strict_types=1);

namespace Phalcon\Incubator\Translate\Tests\Functional\Adapter;

use FunctionalTester;
use Phalcon\Db\Adapter\Pdo\Sqlite;
use Phalcon\Incubator\Translate\Adapter\Database;
use Phalcon\Translate\InterpolatorFactory;

final class DatabaseCest
{
    private $connection;
    private $tableName = 'translations';

    public function __construct()
    {
        $dbFile = codecept_output_dir('test.sqlite');
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }

        $this->connection = new Sqlite([
            'dbname' => $dbFile,
        ]);

        $sql = <<<SQL
CREATE TABLE `{$this->tableName}` (
    `language` VARCHAR(5) NOT NULL,
    `key_name` VARCHAR(48) NOT NULL,
    `value` TEXT NOT NULL
)
SQL;
        $this->connection->execute($sql);

        $data = [
            [
                'language' => 'en',
                'key_name' => 'replace_me',
                'value' => 'Replace me!',
            ],
            [
                'language' => 'pt',
                'key_name' => 'replace_me',
                'value' => 'Muda-me!',
            ],
            [
                'language' => 'ru',
                'key_name' => 'replace_me',
                'value' => 'Поменяй меня!',
            ]
        ];

        foreach ($data as $row) {
            $this->connection->insertAsDict($this->tableName, $row);
        }
    }

    public function translate(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();
        $translatePt = $this->initTranslate('pt');

        $I->assertSame('non-key', $translate->t('non-key'));
        $I->assertSame('Replace me!', $translate->t('replace_me'));
        $I->assertSame('Muda-me!', $translatePt->t('replace_me'));
    }

    public function translateAlias(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();

        $I->assertSame('non-key', $translate->_('non-key'));
        $I->assertSame('Replace me!', $translate->_('replace_me'));
    }

    public function offsetExists(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();

        $I->assertFalse($translate->offsetExists('non-key'));
        $I->assertTrue($translate->offsetExists('replace_me'));
    }

    public function offsetGet(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();

        $I->assertSame('non-key', $translate->offsetGet('non-key'));
        $I->assertSame('Replace me!', $translate->offsetGet('replace_me'));
    }

    public function offsetSet(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();

        $this->connection->insertAsDict($this->tableName, [
            'language' => 'en',
            'key_name' => 'update',
            'value' => 'Update value',
        ]);

        $I->assertSame('Update value', $translate->offsetGet('update'));

        $translate->offsetSet('update', 'Update value 2');
        $I->assertSame('Update value 2', $translate->offsetGet('update'));

        /**
         * Auto insert test
         */
        $nonExistingKey = 'non-exist-key';
        $nonExistingValue = 'Non Existing Value';
        $I->assertFalse($translate->offsetExists($nonExistingKey));
        $translate->offsetSet($nonExistingKey, $nonExistingValue);
        $I->assertSame($nonExistingValue, $translate->offsetGet($nonExistingKey));
    }

    public function offsetUnset(FunctionalTester $I): void
    {
        $translate = $this->initTranslate();

        $this->connection->insertAsDict($this->tableName, [
            'language' => 'en',
            'key_name' => 'for_delete',
            'value' => 'For delete',
        ]);

        $I->assertTrue($translate->offsetExists('for_delete'));
        $translate->offsetUnset('for_delete');
        $I->assertFalse($translate->offsetExists('for_delete'));
    }

    /**
     * @param string $language
     * @return Database
     */
    private function initTranslate($language = 'en'): Database
    {
        return new Database(
            $this->connection,
            $this->tableName,
            $language,
            new InterpolatorFactory()
        );
    }
}
