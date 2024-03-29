<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-24 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @license   Subject matter of licence is the software Asit.
 *            The above copyright, link, package and version notices,
 *            this licence notice shall be included in all copies or substantial
 *            portions of the Asit.
 *
 *            Asit is free software: you can redistribute it and/or modify
 *            it under the terms of the GNU Lesser General Public License as
 *            published by the Free Software Foundation, either version 3 of
 *            the License, or (at your option) any later version.
 *
 *            Asit is distributed in the hope that it will be useful,
 *            but WITHOUT ANY WARRANTY; without even the implied warranty of
 *            MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *            GNU Lesser General Public License for more details.
 *
 *            You should have received a copy of the GNU Lesser General Public License
 *            along with Asit. If not, see <https://www.gnu.org/licenses/>.
 */
declare( strict_types = 1 );
namespace Kigkonsult\Asit;

use PHPUnit\Framework\TestCase;

class AsitBaseTest extends TestCase
{
    /**
     * @param int $max
     * @return array
     */
    public static function arrayLoader( int $max = 100 ) : array
    {
        $output = [];
        for( $ix=0; $ix < $max; $ix++ ) {
            $output['key' . $ix] = 'element' . $ix;
        } // end for
        return $output;
    }

    /**
     * @var array|string[]
     */
    public static array $COLORS = [
        0 => 'Black',
        1 => 'Gray',
        2 => 'Blue',
        3 => 'Green',
        4 => 'Yellow',
        5 => 'Brown',
        6 => 'Orange',
        7 => 'Red',
        8 => 'Pink',
        9 => 'Purple'
    ];

    /**
     * Return colour
     *
     * @param int $index
     * @return string
     */
    public static function getAttribute( int $index ) : string
    {
        $cIx = $index % 10;
        return self::$COLORS[$cIx];
    }
}
