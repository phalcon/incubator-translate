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

use Phalcon\Incubator\Translate\Adapter\CsvMulti;
use Phalcon\Incubator\Translate\Test\Fixtures\Traits\TranslateCsvMultiTrait;
use Phalcon\Translate\Exception;
use Phalcon\Translate\InterpolatorFactory;
use UnitTester;

class ArrayAccessCest
{
    use TranslateCsvMultiTrait;

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: array access
     *
     * @param UnitTester $I
     *
     * @throws Exception
     */
    public function translateAdapterCsvMultiWithArrayAccess(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - array access');

        $csv = $this->getCsvConfig();

        $translator = new CsvMulti(
            $csv,
            "en_US",
            new InterpolatorFactory()
        );

        $I->assertTrue(
            isset(
                $translator['label_street']
            )
        );

        $I->assertFalse(
            isset(
                $translator['label_city']
            )
        );
    }
}
