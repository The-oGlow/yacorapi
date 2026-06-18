<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace oglow\tools\Yacorapi\Statistic;

use PHPUnit\Framework\EasyGoingTestCase;

/**
 * Description of PagetypeStatisticTest
 *

 */
class PagetypeStatisticTest extends EasyGoingTestCase {

    #[\Override]
    protected function getCasto2t(): PagetypeStatistic {
        return $this->o2t;
    }

    #[\Override]
    protected static function prepareO2t(): mixed {
        return new PagetypeStatistic('');
    }

    public function testToString(): void {
        $expected = sprintf("%s:[%s,%s]",
                PagetypeStatisticTest::class,
                PagetypeStatistic::EXPORT_NAME, '{}');

        $actual = $this->getCasto2t()->__toString();

        $this->assertEquals($expected, $actual);
    }
}
