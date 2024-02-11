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

use ArrayIterator;
use Exception;
use OutOfBoundsException;
use Traversable;

class ItTest extends AsitBaseTest
{
    /**
     * @test It isCollectionSet, exists, count,
     *
     */
    public function itTest1() : void
    {
        $it = new It();

        $this->assertFalse(
            $it->isCollectionSet(),
            'test10'
        );

        foreach( self::arrayLoader() as $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            $it->isCollectionSet(),
            'test11'
        );

        $this->assertEquals(
            100, $it->count(), 'test12'
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
    public function itTest2() : void
    {
        $it1  = It::getInstance( self::arrayLoader());
        $cnt1 = $it1->count();

        $it2 = It::getInstance();

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
    public function itTest21() : void
    {

        $it1 = new It();
        foreach( self::arrayLoader() as $value ) {
            $it1->append( $value );
        } // end for

        $asit1 = new Asit();
        foreach( self::arrayLoader() as $value ) {
            $asit1->append( $value );
        } // end for

        foreach(
            [
                $it1,                             // loaded using append
                new It( self::arrayLoader()),    // loaded using array
                new It( $it1 ),                   // loaded using It, Traversable
                new It( new ArrayIterator( self::arrayLoader())), // any Traversable
                $asit1,                           // loaded using append
                new Asit( self::arrayLoader()),  // loaded using array
                new Asit( $asit1 ),               // loaded using Asit, Traversable
                new Asit( new ArrayIterator( self::arrayLoader())) // any Traversable
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
    public function itTest21test( int $case, Traversable $it ) : void
    {
        ++$case;

        $this->assertEquals(
            100, $it->count(), __FUNCTION__ . ' #1-' . $case . ' exp: 100, got: ' . $it->count()
        );

        $it->rewind();               // test rewind
        $this->assertEquals(
            0, $it->key(), __FUNCTION__ . ' #2-' . $case
        );
        $this->assertEquals(
            'element0', $it->current(), __FUNCTION__ . ' #3-' . $case
        );

        $it->next();             // test next
        $this->assertEquals(
            1, $it->key(), __FUNCTION__ . ' #4-' . $case . ' exp: 1. got: ' . $it->key()
        );
        $this->assertEquals(
            'element1', $it->current(), __FUNCTION__ . ' #5-' . $case
        );

        $it->last();           // test last
        $this->assertEquals(
            99, $it->key(), __FUNCTION__ . ' #7-' . $case . ' exp: 999, got: ' . $it->key()
        );
        $this->assertEquals(
            'element99', $it->current(), __FUNCTION__ . ' #8-' . $case
        );

        $it->previous();    // test previous
        $this->assertEquals(
            98, $it->key(), __FUNCTION__ . ' #10-' . $case
        );
        $this->assertEquals(
            'element98', $it->current(), __FUNCTION__ . ' #11-' . $case
        );

        $it->last();
        $it->next();
        $this->assertEquals(
            100, $it->key(), __FUNCTION__ . ' #14-' . $case
        );
        $this->assertFalse(
            $it->valid(),
            'test21-14-' . $case
        );

        $it->rewind();
        $it->previous();
        $this->assertEquals(
            -1, $it->key(), __FUNCTION__ . ' #15-' . $case
        );
        $this->assertFalse(
            $it->valid(),
            __FUNCTION__ . ' #16-' . $case
        );

        $it->seek( 0 );   // test seek
        $this->assertEquals(
            0, $it->key(), __FUNCTION__ . ' #17-' . $case
        );
        $this->assertEquals(
            'element0', $it->current(), __FUNCTION__ . ' #18-' . $case
        );

        $this->assertEquals(
            'element49', $it->seek( 49 )->current(), __FUNCTION__ . ' #19-' . $case
        );

        $it->seek( 50 );
        $this->assertEquals(
            50, $it->key(), __FUNCTION__ . ' #20-' . $case
        );
        $this->assertEquals(
            'element50', $it->current(), __FUNCTION__ . ' #21-' . $case
        );

    }

    /**
     * Test It / Asit multiple setCollections, note, Asit requires unique pkeys
     *
     * @test
     */
    public function ItTest22() : void
    {
        $payLoad1 = array_values( self::arrayLoader());
        $payLoad2 = array_combine( range( 100, 199 ), self::arrayLoader());
        foreach( [ It::factory( $payLoad1 ) , Asit::factory( $payLoad1 ) ] as $it ) {
            $it->setCollection( $payLoad2 );
            $this->assertEquals(     // test count
                200, $it->count(), __FUNCTION__ . ' #1 exp: 200, got: ' . $it->count()
            );
            $it->rewind();         // test rewind
            $this->assertEquals(
                0, $it->key(), __FUNCTION__ . ' #2'
            );
            $it->last();           // test last
            $this->assertEquals(
                199, $it->key(), __FUNCTION__ . ' #3'
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
    public function itTest25() : void
    {

        $it = new It();
        foreach( self::arrayLoader() as $value ) {
            $it->append( $value );
        } // end for

        $this->assertTrue(
            ( $it->GetIterator() instanceof Traversable ),   // test GetIterator - Traversable
            __FUNCTION__ . ' #1'
        );
        $this->assertTrue(
            ( $it instanceof Traversable ),  // test Asit - Traversable
            __FUNCTION__ . ' #2'
        );

        // testing Traversable, i.e. makes the class traversable using foreach
        $cnt   = 0;
        $value = null;
        foreach(   $it   as $value ) {
            ++$cnt;
        }
        $this->assertEquals( 100, $cnt, __FUNCTION__ . ' #3' );
        $this->assertEquals(
            'element99', $value, __FUNCTION__ . ' #5'
        );

        // testing yield() as a foreach $instance
        $cnt   = 0;
        $value = null;
        foreach( $it->yield() as $value ) {
            ++$cnt;
        }
        $this->assertEquals( 100, $cnt, __FUNCTION__ . ' #7' );
        $this->assertEquals(
            'element99', $value, __FUNCTION__ . ' #9'
        );


    }

    /**
     * Test It seek + OutOfBoundsException
     *
     * @test
     */
    public function itTest26() : void
    {
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
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' exp 2, got ' . $ok );
    }

    protected static array $collection = [ 'value9-2', 'value1-3', 'Value3-1' ];

    /**
     * Test It sort
     *
     * @test
     */
    public function itTest32() : void
    {
        $it = It::factory( self::$collection );

        $result  = $it->get(); // ignore keys
        $result1 = reset( $result );
        $this->assertEquals(
            'value9-2',
            $result1,
            __FUNCTION__ . ' #1 exp "value9-2",  got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );

        $result  = $it->get( SORT_FLAG_CASE | SORT_STRING );
        $result1 = reset( $result );
        $this->assertEquals(
            'value1-3',
            $result1,
            __FUNCTION__ . ' #2 exp "value1-3", got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );

        $result  = $it->get( [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'Value3-1',
            $result1,
            __FUNCTION__ . ' #3 exp "value3-1", got : ' . str_replace( PHP_EOL, '', var_export( $result, true  ))
        );
    }

    /**
     * @param string $a
     * @param string $b
     * @return int
     */
    public static function cmp( string $a, string $b ) : int
    {
        $aLast = substr( $a, -1 );
        $bLast = substr( $b, -1 );
        if( $aLast == $bLast ) {
            return 0;
        }
        return ( $aLast < $bLast ) ? -1 : +1;
    }

    /**
     * @test It isCollectionSet, last, previous, remove
     *
     */
    public function itTest4() : void
    {
        $it = It::factory();
        $this->assertEquals( 0, $it->key(), __FUNCTION__ . ' #1');

        $it->last();
        $this->assertEquals( 0, $it->key(), __FUNCTION__ . ' #2');

        $it->setCollection( self::arrayLoader(5 ));
        $this->assertTrue( $it->isCollectionSet(), __FUNCTION__ . ' #3' );

        $count = $it->count();
        $this->assertEquals(
            5, $count, __FUNCTION__ . ' #4, exp: 5, got: ' . $count
        );

        $it->last();
        $key = $it->key();
        $this->assertEquals( 4, $key, __FUNCTION__ . ' #5, exp 4, got: ' . $key );

        $it->previous();
        $key = $it->key();
        $this->assertEquals( 3, $key, __FUNCTION__ . ' #6, exp 3, got: ' . $key );

        $it->remove();
        $this->assertFalse( $it->exists( $key ), __FUNCTION__ . ' #7' );

        $it->rewind();
        $key = $it->key();
        $this->assertEquals( 0, $key, __FUNCTION__ . ' #8, exp 0, got: ' . $key );

        $it->last();
        $key = $it->key();
        $this->assertEquals( 4, $key, __FUNCTION__ . ' #9, exp 4, got: ' . $key );

        $it->previous();
        $it->previous();
        $key = $it->key();
        $this->assertEquals( 1, $key, __FUNCTION__ . ' #10, exp 1, got: ' . $key );

        $it->next();
        $key = $it->key();
        $this->assertEquals( 2, $key, __FUNCTION__ . ' #11, exp 2, got: ' . $key );

        $it->next();
        $key = $it->key();
        $this->assertEquals( 4, $key, __FUNCTION__ . ' #12, exp 4, got: ' . $key );

        $it->last();
        while( $it->valid()) {
            $it->remove();
            $it->previous();
        }
        $this->assertEquals( -1, $it->key(), __FUNCTION__ . ' #13');
    }
}
