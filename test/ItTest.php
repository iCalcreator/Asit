<?php
/**
 * Asit package manages assoc arrays
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

use ArrayIterator;
use Exception;
use OutOfBoundsException;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;
use Traversable;

class ItTest extends TestCase
{

    public function arrayLoader( $max = 100 ) {
        $output = [];
        for( $ix=0; $ix < $max; $ix++ ) {
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
     * @test It isCollectionSet, exists, count,
     *
     */
    public function itTest1() {
        $it = new It();

        $this->assertFalse(
            $it->isCollectionSet(),
            'test10'
        );

        foreach( $this->arrayLoader() as  $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            $it->isCollectionSet(),
            'test11'
        );

        $this->assertTrue(
            ( 100 == $it->count()),
            'test12'
        );

        $this->assertTrue(
            $it->exists( 0 ),
            'test13'
        );
        $this->assertFalse(
            $it->exists( -1 ),
            'test14'
        );
        $this->assertFalse(
            $it->exists( -1111 ),
            'test15'
        );
        $this->assertTrue(
            $it->exists( 99 ),
            'test16'
        );
        $this->assertFalse(
            $it->exists( 100 ),
            'test17'
        );
        $this->assertFalse(
            $it->exists( 1000 ),
            'test18'
        );
    }

    /**
     * @test It singleton
     *
     */
    public function itTest2() {
        $it1  = It::singleton( $this->arrayLoader());
        $cnt1 = $it1->count();

        $it2 = It::singleton();

        $this->assertEquals(
            $cnt1,
            $it2->count(),
            'test2-1'
        );
    }

    /**
     * Testing It + Asit append + constructor/setCollection array+Traversable
     *
     * @test
     */
    public function itTest21() {

        $it1 = new It();
        foreach( $this->arrayLoader() as $value ) {
            $it1->append( $value );
        } // end for

        $asit1 = new Asit();
        foreach( $this->arrayLoader() as $value ) {
            $asit1->append( $value );
        } // end for

        foreach(
            [
                $it1,                             // loaded using append
                new It( $this->arrayLoader()),    // loaded using array
                new It( $it1 ),                   // loaded using It, Traversable
                new It( new ArrayIterator( $this->arrayLoader())), // any Traversable
                $asit1,                           // loaded using append
                new Asit( $this->arrayLoader()),  // loaded using array
                new Asit( $asit1 ),               // loaded using Asit, Traversable
                new Asit( new ArrayIterator( $this->arrayLoader())) // any Traversable
            ] as $ix => $it ) {
            $this->itTest21test( $ix, $it );
        }
    }

    /**
     * Testing It Iterator methods excl GetIterator - Traversable
     *
     * @param int $case
     * @param Traversable $it
     */
    public function itTest21test( $case, Traversable $it ) {
        $case += 1;

        $this->assertTrue(
            ( 100 == $it->count()),
            'test21-0-' . $case . ' exp: 100, got: ' . $it->count()
        );

        $it->rewind();               // test rewind
        $this->assertTrue(
            0 == $it->key(),
            'test21-1-' . $case
        );
        $this->assertTrue(
            'element0' == $it->current(),
            'test21-2-' . $case
        );

        $it->next();             // test next
        $this->assertTrue(
            1 == $it->key(),
            'test21-4-' . $case . ' exp: 1. got: ' . $it->key()
        );
        $this->assertTrue(
            'element1' == $it->current(),
            'test21-5-' . $case
        );

        $it->last();           // test last
        $this->assertTrue(
            99 == $it->key(),
            'test21-7-' . $case . ' exp: 999, got: ' . $it->key()
        );
        $this->assertTrue(
            'element99' == $it->current(),
            'test21-8-' . $case
        );

        $it->previous();    // test previous
        $this->assertTrue(
            98 == $it->key(),
            'test21-10-' . $case
        );
        $this->assertTrue(
            'element98' == $it->current(),
            'test21-11-' . $case
        );

        $it->last();
        $it->next();
        $this->assertTrue(
            100 == $it->key(),
            'test21-13-' . $case
        );
        $this->assertFalse(
            $it->valid(),
            'test21-14-' . $case
        );

        $it->rewind();
        $it->previous();
        $this->assertTrue(
            -1 == $it->key(),
            'test21-15-' . $case
        );
        $this->assertFalse(
            $it->valid(),
            'test21-16-' . $case
        );

        $it->seek( 0 );   // test seek
        $this->assertTrue(
            0 == $it->key(),
            'test21-17-' . $case
        );
        $this->assertTrue(
            'element0' == $it->current(),
            'test21-18-' . $case
        );

        $it->seek( 50 );
        $this->assertTrue(
            50 == $it->key(),
            'test21-20-' . $case
        );
        $this->assertTrue(
            'element50' == $it->current(),
            'test21-21-' . $case
        );

    }

    /**
     * Test It / Asit setCollection + InvalidArgumentException
     *
     * @test
     */
    public function ItTest21exception() {
        $invalid = new stdClass;
        foreach( [ It::factory(), Asit::factory() ] as $it ) {
            $ok = 0;
            try {
                $it->setCollection( $invalid );
                $ok = 1;
            }
            catch( InvalidArgumentException $e ) {
                $ok = 2;
            }
            catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test21 exception, exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * Test It / Asit multiple setCollections, note, Asit requires unique pkeys
     *
     * @test
     */
    public function ItTest22() {
        $payLoad1 = array_values( $this->arrayLoader());
        $payLoad2 = array_combine( range( 100, 199 ), $this->arrayLoader());
        foreach( [ It::factory( $payLoad1 ) , Asit::factory( $payLoad1 ) ] as $it ) {
            $it->setCollection( $payLoad2 );
            $this->assertTrue(     // test count
                200 == $it->count(),
                'test22-1 exp: 200, got: ' . $it->count()
            );
            $it->rewind();         // test rewind
            $this->assertTrue(
                0 == $it->key(),
                'test22-2'
            );
            $it->last();           // test last
            $this->assertTrue(
                199 == $it->key(),
                'test22-3'
            );
        }
    }

    /**
     * Testing It IteratorAggregate interface - Traversable
     *     method GetIterator + getPkeyIterator
     *
     *
     * @test
     */
    public function itTest25() {

        $it = new It();
        foreach( $this->arrayLoader() as $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            ( $it->GetIterator() instanceof Traversable ),   // test GetIterator - Traversable
            'test25-1'
        );
        $this->assertTrue(
            ( $it instanceof Traversable ),  // test Asit - Traversable
            'test25-2'
        );

        // testing Traversable, i.e. makes the class traversable using foreach
        $cnt = 0;
        foreach(   $it   as $key => $value ) { // 'internal key', NOT pKey
            $cnt += 1;
        }
        $this->assertTrue( ( 100 == $cnt ), 'test92-3' );
        $this->assertTrue(
            99 == $key,
            'test25-4'
        );
        $this->assertTrue(
            ( 'element99' == $value ),
            'test25-5'
        );

    }

    /**
     * Test It seek + OutOfBoundsException
     *
     * @test
     */
    public function itTest26() {
        $it = It::factory( [ 1 => 'value' ] );
        $ok = 0;
        try {
            $it->seek( 26 );
            $ok = 1;
        }
        catch( OutOfBoundsException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test26, exp 2, got ' . $ok );
    }

    protected static  $collection = [ 'value9-2', 'value1-3', 'Value3-1' ];

    /**
     * Test It sort - InvalidArgumentException
     *
     * @test
     */
    public function itTest31() {
        $it = new It( self::$collection );
        $ok = 0;
        try {
            $it->get( [] );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test31-1, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $it->get( 'dummy' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test31-2, exp 2, got ' . $ok );
    }

    /**
     * Test It sort
     *
     * @test
     */
    public function itTest32() {
        $it = It::factory( self::$collection );

        $result  = $it->get(); // ignore keys
        $result1 = reset( $result );
        $this->assertEquals(
            'value9-2',
            $result1,
            'test 32-1 exp "value9",  got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );

        $result  = $it->get( SORT_FLAG_CASE | SORT_STRING );
        $result1 = reset( $result );
        $this->assertEquals(
            'value1-3',
            $result1,
            'test 32-2 exp "value1", got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );

        $result  = $it->get( [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'Value3-1',
            $result1,
            'test 32-2 exp "value1", got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );
    }

    public static function cmp( $a, $b ) {
        $aLast = substr( $a, -1 );
        $bLast = substr( $b, -1 );
        if( $aLast == $bLast ) {
            return 0;
        }
        return ( $aLast < $bLast ) ? -1 : +1;
    }

}
