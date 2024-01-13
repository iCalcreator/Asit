<?php
/**
 * Asit package manages assoc arrays
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

class TestClass
{
    private int $value;

    public static function factory( int $value ) : self
    {
        $instance = new self();
        $instance->setValue( $value );
        return $instance;
    }

    /**
     * @return int
     */
    public function getValue() : int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return TestClass
     */
    public function setValue( int $value ) : TestClass
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Sort on ascending value
     *
     * @param TestClass $a
     * @param TestClass $b
     * @return int
     */
    public static function sort1( TestClass $a, TestClass $b ) : int
    {
        return strcasecmp((string) $a->getValue(), (string) $b->getValue());
    }

    /**
     * Sort on descending value
     *
     * @param TestClass $a
     * @param TestClass $b
     * @return int
     */
    public static function sort2( TestClass $a, TestClass $b ) : int
    {
        return strcasecmp((string) $b->getValue(), (string) $a->getValue());
    }

}
class AsitListTest2 extends TestCase
{
    /**
     * Test method 'tagGet' with excludes and callable sorter, search on tags and lastly, 'pKeyTagGet'  with pKey
     *
     * @test
     * @return void
     */
    public function AsitListTest201() : void
    {
        static $KEY  = 'key';
        static $TAG1 = 'tag1';
        static $TAG2 = 'tag2';
        static $TAG3 = 'tag3';
        static $TAG4 = 'tag4';
        static $TAG5 = 'tag5';
        $value0 = TestClass::factory( 0 );
        $value1 = TestClass::factory( 1 );
        $value2 = TestClass::factory( 2 );
        $value3 = TestClass::factory( 3 );
        $value4 = TestClass::factory( 4 );
        $value5 = TestClass::factory( 5 );
        $value6 = TestClass::factory( 6 );
        $value7 = TestClass::factory( 7 );
        $sort1  = [ TestClass::class, 'sort1' ];
        $sort2  = [ TestClass::class, 'sort2' ];
        $exp1   = [ 2 => clone $value2, 4 => clone $value4 ];
        $exp2   = [ 1 => clone $value1, 3 => clone $value3, 5 => clone $value5 ];
        $exp3   = [ 1 => clone $value1, 4 => clone $value4, 5 => clone $value5 ];
        $exp4   = [ 4 => clone $value4, 1 => clone $value1, ];

        $list = AsittagList::factory( null, TestClass::class )
            ->append( $value0, $KEY . 0, $TAG1 )
            ->append( $value1, $KEY . 1, [ $TAG1, $TAG5 ] )
            ->append( $value2, $KEY . 2, $TAG2 )
            ->append( $value3, $KEY . 3, [ $TAG1, $TAG2 ] )
            ->append( $value4, $KEY . 4, [ $TAG2, $TAG5 ] )
            ->append( $value5, $KEY . 5, [ $TAG1, $TAG4 ] )
            ->append( $value6, $KEY . 6, [ $TAG1, $TAG2, $TAG3 ] )
            ->append( $value7, $KEY . 7, $TAG4 );

        $this->assertEquals( 8, $list->count(), __FUNCTION__ . ', error 1' );

        $list->rewind();
        $list->remove();
        $list->last();
        $list->remove();
        $this->assertEquals( 6, $list->count(), __FUNCTION__ . ', error 2' );

        $act = $list->tagGet( $TAG4 );
        $this->assertEquals( 1, count( $act ), __FUNCTION__ . ', error 3' );

        $act = $list->tagGet( $TAG2, true, [ $TAG1, $TAG3 ] ); // excl all with 'tag1', 'tag3'
        $this->assertEquals( $exp1, $act, __FUNCTION__ . ', error 4' );

        // output is in input order, only tag 'tag1'
        $act = $list->tagGet( $TAG1, null, [ $TAG3 ] );
        $this->assertEquals( $exp2, $act, __FUNCTION__ . ', error 5'
        );

        // output in 'sorter' order
        $act = $list->tagGet( $TAG1,true, $TAG3, $sort1  );
        uasort( $exp2, $sort1  );
        $this->assertEquals( $exp2, $act, __FUNCTION__ . ', error 6' );

        // pkey search
        $pkeySearch = [ $KEY . 1, $KEY . 4, $KEY . 5 ];
        $act = $list->pKeyTagGet( $pkeySearch );
        $this->assertEquals( $exp3, $act, __FUNCTION__ . ', error 7' );

        // pkey search with tag exclude and sort
        $act = $list->pKeyTagGet( $pkeySearch, null, null, $TAG4, $sort2 );
        $this->assertEquals( $exp4, $act, __FUNCTION__ . ', error 8' );
    }
}
