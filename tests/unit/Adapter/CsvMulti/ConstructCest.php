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

namespace Phalcon\Incubator\Translate\Tests\Unit\Adapter\CsvMulti;

use ArrayAccess;
use Phalcon\Incubator\Translate\Adapter\CsvMulti;
use Phalcon\Incubator\Translate\Test\Fixtures\Traits\TranslateCsvMultiTrait;
use Phalcon\Translate\Adapter\AdapterInterface;
use Phalcon\Translate\Exception;
use Phalcon\Translate\InterpolatorFactory;
use UnitTester;

class ConstructCest
{
    use TranslateCsvMultiTrait;

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: __construct()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterCsvConstruct(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - constructor');

        $csv = $this->getCsvConfig();

        $translator = new CsvMulti(
            $csv,
            "en_US",
            new InterpolatorFactory()
        );

        $I->assertInstanceOf(
            ArrayAccess::class,
            $translator
        );

        $I->assertInstanceOf(
            AdapterInterface::class,
            $translator
        );
    }

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: __construct()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterCsvConstructWithBadLocale(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - constructor with bad locale');

        $I->expectThrowable(
            new Exception("The locale 'ru_RU' is not available in the data source."),
            function () {
                $csv = $this->getCsvConfig();

                $translator = new CsvMulti(
                    $csv,
                    "ru_RU",
                    new InterpolatorFactory()
                );
            }
        );
    }
}
