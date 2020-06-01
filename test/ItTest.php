<?php
/**
 * Asit manages assoc arrays
 *
 * Copyright 2020 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * Link <https://kigkonsult.se>
 * Support <https://github.com/iCalcreator/Asit>
 *
 * This file is part of Asit.
 *
 * Asit is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * Asit is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with Asit. If not, see <https://www.gnu.org/licenses/>.
 */
namespace Kigkonsult\Asit;

use Exception;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Traversable;

class ItTest extends TestCase
{

    public function arrayLoader() {

        $output = [];
        for( $ix=0; $ix < 1000; $ix++ ) {
            $output['key' . $ix] = 'element' . $ix;
        } // end for

        return $output;
    }

    public static $COLORS = [
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

    public function getAttribute( $index ) {
        $cIx = $index % 10;
        return self::$COLORS[$cIx];
    }

    /**
     * @test It exists, count,
     *
     */
    public function itTest1() {
        $it = new It();
        foreach( $this->arrayLoader() as  $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            ( 1000 == $it->count()),
            'test11'
        );

        $this->assertTrue(
            $it->exists( 0 ),
            'test12'
        );
        $this->assertFalse(
            $it->exists( -1 ),
            'test13'
        );
        $this->assertFalse(
            $it->exists( -1111 ),
            'test14'
        );
        $this->assertTrue(
            $it->exists( 999 ),
            'test15'
        );
        $this->assertFalse(
            $it->exists( 1000 ),
            'test16'
        );
        $this->assertFalse(
            $it->exists( 100000 ),
            'test7'
        );
    }

    /**
     * Testing It Iterator methods excl GetIterator - Traversable
     *
     * @test
     */
    public function itTest21() {

        $it = new It();
        foreach( $this->arrayLoader() as $value ) {
            $it->append( $value );
        } // end for

        $it->rewind();               // test rewind
        $this->assertTrue(
            0 == $it->key(),
            'test21-1'
        );
        $this->assertTrue(
            'element0' == $it->current(),
            'test21-2'
        );

        $it->next();             // test next
        $this->assertTrue(
            1 == $it->key(),
            'test21-4'
        );
        $this->assertTrue(
            'element1' == $it->current(),
            'test21-5'
        );

        $it->last();           // test last
        $this->assertTrue(
            999 == $it->key(),
            'test21-7'
        );
        $this->assertTrue(
            'element999' == $it->current(),
            'test21-8'
        );

        $it->previous();    // test previous
        $this->assertTrue(
            998 == $it->key(),
            'test21-10'
        );
        $this->assertTrue(
            'element998' == $it->current(),
            'test21-11'
        );

        $it->last();
        $it->next();
        $this->assertTrue(
            1000 == $it->key(),
            'test21-13'
        );
        $this->assertFalse(
            $it->valid(),
            'test21-14'
        );

        $it->rewind();
        $it->previous();
        $this->assertTrue(
            -1 == $it->key(),
            'test21-15'
        );
        $this->assertFalse(
            $it->valid(),
            'test21-16'
        );

        $it->seek( 0 );   // test seek
        $this->assertTrue(
            0 == $it->key(),
            'test21-17'
        );
        $this->assertTrue(
            'element0' == $it->current(),
            'test21-18'
        );

        $it->seek( 50 );
        $this->assertTrue(
            50 == $it->key(),
            'test21-20'
        );
        $this->assertTrue(
            'element50' == $it->current(),
            'test21-21'
        );

    }

    /**
     * Testing It IteratorAggregate interface - Traversable
     *     method GetIterator + getPkeyIterator
     *
     *
     * @test
     */
    public function itTest22() {

        $it = new It();
        foreach( $this->arrayLoader() as $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            ( $it->GetIterator() instanceof Traversable ),   // test GetIterator - Traversable
            'test22-1'
        );
        $this->assertTrue(
            ( $it instanceof Traversable ),  // test Asit - Traversable
            'test22-2'
        );

        // testing Traversable, i.e. makes the class traversable using foreach
        $cnt = 0;
        foreach(   $it   as $key => $value ) { // 'internal key', NOT pKey
            $cnt += 1;
        }
        $this->assertTrue( ( 1000 == $cnt ), 'test92-3' );
        $this->assertTrue(
            999 == $key,
            'test22-4'
        );
        $this->assertTrue(
            ( 'element999' == $value ),
            'test22-5'
        );

    }

    /**
     * Test It seek + OutOfBoundsException
     *
     * @test
     */
    public function itTest23() {
        $it = It::factory( [ 1 => 'value' ] );
        $ok = 0;
        try {
            $it->seek( 23 );
            $ok = 1;
        }
        catch( OutOfBoundsException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test23, exp 2, got ' . $ok );
    }

}
