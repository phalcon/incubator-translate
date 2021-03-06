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

class OffsetSetCest
{
    use TranslateCsvMultiTrait;

    /**
     * Tests Phalcon\Incubator\Translate\Adapter\CsvMulti :: offsetSet()
     *
     * @author Phalcon Team <team@phalcon.io>
     */
    public function translateAdapterCsvOffsetSet(UnitTester $I)
    {
        $I->wantToTest('Translate\Adapter\CsvMulti - offsetSet() throws exception');

        $I->expectThrowable(
            new Exception('Translate is an immutable ArrayAccess object'),
            function () {
                $csv = $this->getCsvConfig();

                $translator = new CsvMulti(
                    $csv,
                    "en_US",
                    new InterpolatorFactory()
                );

                $translator->offsetSet('team', 'Team');
            }
        );
    }
}
