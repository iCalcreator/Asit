<?php
/**
 * Asit package manages assoc arrays
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
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

use Exception;
use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Traversable;

class Asit1Test extends TestCase
{

    public function arrayLoader( $max = 1000 ) : array
    {

        $output = [];
        for( $ix=0; $ix < $max; $ix++ ) {
            $output['key' . $ix] = 'element' . $ix;
        } // end for

        return $output;
    }

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

    public static function getAttribute( $index ) : string
    {
        $cIx = $index % 10;
        return self::$COLORS[$cIx];
    }

    /**
     * @test Asit exists, count,
     *
     */
    public function asitTest1() : void
    {
        $asit = new Asit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $this->assertEquals(
            100, $asit->count(), 'test11'
        );

        $this->assertTrue(
            $asit->exists( 0 ),
            'test12'
        );
        $this->assertFalse(
            $asit->exists( -1 ),
            'test13'
        );
        $this->assertFalse(
            $asit->exists( -1111 ),
            'test14'
        );
        $this->assertTrue(
            $asit->exists( 99 ),
            'test15'
        );
        $this->assertFalse(
            $asit->exists( 100 ),
            'test16'
        );
        $this->assertFalse(
            $asit->exists( 100000 ),
            'test7'
        );
        $asit = null;
    }

    /**
     * Testing Asit Iterator methods excl GetIterator - Traversable
     *
     * @test
     */
    public function asitTest21() : void
    {

        $asit = new Asit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $asit->rewind();               // test rewind
        $this->assertEquals(
            0, $asit->key(), 'test21-1'
        );
        $this->assertEquals(
            'element0', $asit->current(), 'test21-2'
        );
        $pKey = $asit->getCurrentPkey();
        $this->assertEquals(
            'key0', $pKey, 'test21-3'
        );

        $asit->next();             // test next
        $this->assertEquals(
            1, $asit->key(), 'test21-4'
        );
        $this->assertEquals(
            'element1', $asit->current(), 'test21-5'
        );
        $this->assertEquals(
            'key1', $asit->getCurrentPkey(), 'test21-6'
        );

        $asit->last();           // test last
        $this->assertEquals(
            99, $asit->key(), 'test21-7'
        );
        $this->assertEquals(
            'element99', $asit->current(), 'test21-8'
        );
        $this->assertEquals(
            'key99', $asit->getCurrentPkey(), 'test21-9'
        );

        $asit->previous();    // test previous
        $this->assertEquals(
            98, $asit->key(), 'test21-10'
        );
        $this->assertEquals(
            'element98', $asit->current(), 'test21-11'
        );
        $this->assertEquals(
            'key98', $asit->getCurrentPkey(), 'test21-12'
        );

        $asit->last();
        $asit->next();
        $this->assertEquals(
            100, $asit->key(), 'test21-13'
        );
        $this->assertFalse(
            $asit->valid(),
            'test21-14'
        );

        $asit->rewind();
        $asit->previous();
        $this->assertEquals(
            -1, $asit->key(), 'test21-15'
        );
        $this->assertFalse(
            $asit->valid(),
            'test21-16'
        );

        $asit->seek( 0 );   // test seek
        $this->assertEquals(
            0, $asit->key(), 'test21-17'
        );
        $this->assertEquals(
            'element0', $asit->current(), 'test21-18'
        );
        $this->assertEquals(
            'key0', $asit->getCurrentPkey(), 'test21-19'
        );

        $asit->seek( 50 );
        $this->assertEquals(
            50, $asit->key(), 'test21-20'
        );
        $this->assertEquals(
            'element50', $asit->current(), 'test21-21'
        );
        $this->assertEquals(
            'key50', $asit->getCurrentPkey(), 'test21-22'
        );

        $asit = null;
    }

    /**
     * Testing Asit/Asmit IteratorAggregate interface - Traversable
     *     method GetIterator + getPkeyIterator
     *
     *
     * @test
     */
    public function asitTest22() : void
    {

        foreach( [ new Asit(), new Asmit() ] as $asit ) {

            foreach( $this->arrayLoader( 100 ) as $key => $value ) {
                $asit->append( $value, $key );
            }

            $this->assertTrue(
                ( $asit->GetIterator() instanceof Traversable ),   // test GetIterator - Traversable
                'test22-1'
            );
            $this->assertTrue(
                ( $asit instanceof Traversable ),  // test Asit - Traversable
                'test22-2'
            );

            // testing Traversable, i.e. makes the class traversable using foreach
            $cnt = 0;
            foreach( $asit as $key => $value ) { // 'internal key', NOT pKey
                ++$cnt;
            }
            $this->assertEquals(
                100, $cnt, 'test92-3'
            );
            $this->assertEquals(
                99, $key, 'test22-4'
            );
            $this->assertEquals(
                'element99', $value, 'test22-5'
            );
            $asit->seek( $key ); // position iterator last
            $this->assertEquals(
                'key99', $asit->getCurrentPkey(), 'test22-6, exp: key99, got: ' . $asit->getCurrentPkey()
            );

            $this->assertTrue(
                ( $asit->GetPkeyIterator() instanceof Traversable ),   // test GetPkeyIterator - Traversable
                'test22-11'
            );
            $cnt = 0;
            foreach( $asit->GetPkeyIterator() as $pKey => $element ) {
                ++$cnt;
                if( 0 === ( $cnt % 50 ) ) {
                    $this->assertEquals(
                        $element,
                        $asit->pKeySeek( $pKey )
                            ->current(),   // test pKeySeek
                        'test22-12'
                    );
                }
            } // end foreach

            $asit = null;
        } // end foreach  asit/asmit
    }

    /**
     * Test Asmit countPkey/removePkey/getCurrentPkey/setPkey exceptions
     *
     * @test
     */
    public function asmitTest23() : void
    {
        try {
            Asmit::factory()->pKeySeek( 'key23' ); // don't exist
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test23-1, exp 2, got ' . $ok );

        try {
            Asmit::factory()->countPkey( 'key23' ); // don't exist
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test23-2, exp 2, got ' . $ok );

        try {
            Asmit::factory( $this->arrayLoader( 10 ))->removePkey( 'test23-3' ); // remove but don't exist
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test23-3, exp 2, got ' . $ok );

        try {
            Asmit::factory()->getCurrentPkey(); // no current
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test23-4, exp 2, got ' . $ok );

        $asmit = Asmit::factory()->append( 'value', 'key' );
        $pKey  = $asmit->getCurrentPkey();
        try {
            $asmit->setCurrentPkey( $pKey ); // set the same primary key again, allowed
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 1, $ok, 'test23-4, exp 1, got ' . $ok );

    }

    /**
     * Test Asmit addCurrentPkey/countPkey/removePkey/getCurrentPkey
     *
     * @test
     */
    public function asmitTest24() : void
    {
        $asmit = new Asmit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asmit->append( $value, $key );
        }

        $asmit->seek( array_rand( array_flip( range( 2, 92 )))); // set current

        $pKey1 = $asmit->getCurrentPkey();
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey1 ),
            'test24-1, exp 1, got ' . $asmit->countPkey( $pKey1 )
        );

        $asmit->removePkey( $pKey1 );
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey1 ),
            'test24-2, exp 1, got ' . $asmit->countPkey( $pKey1 )
        );

        $pKey2 = 'test24';
        $asmit->addCurrentPkey( 'test24' );
        $this->assertEquals(
            2,
            $asmit->countPkey( $pKey2 ),
            'test24-3, exp 2, got ' . $asmit->countPkey( $pKey2 )
        );

        $asmit->removePkey( $pKey1 );
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey2 ),  // <-------
            'test24-4, exp 1, got ' . $asmit->countPkey( $pKey2 )
        );
        $this->assertFalse(
            $asmit->pKeyExists( $pKey1 ),
            'test24-5, found : ' . $pKey1
        );

        $pKey3 = 'test24-3';
        $asmit->addCurrentPkey( $pKey3 );
        $exp   = [ $pKey2, $pKey3 ];
        $pKeys = $asmit->getCurrentPkey( false);
        $this->assertEquals(
            $exp,
            $pKeys,
            'test24-5, exp : ' . implode( ',', $exp ) . ',  got : ' . implode( ',', $pKeys )
        );

        $iterator = $asmit->getPkeyIterator();
        $this->assertTrue(
            ( $iterator instanceof Traversable ),   // test getPkeyIterator - Traversable
            'test24-6'
        );
        $this->assertEquals(
            100,
            $iterator->count(),
            'test24-7, exp : 100,  got : ' . $iterator->count()
        );

        $pKeys = [];
        foreach( $iterator as $key => $value ) {
            $pKeys[] = $key;
        }
        $this->assertContains(
            $pKey2, $pKeys, 'test24-8'
        );

        $asmit = null;
    }

    /**
     * Test Asmit get
     *
     * @test
     */
    public function asmitTest25() : void
    {
        $asmit = new Asmit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asmit->append( $value, $key );
        }

        $asmit->seek( array_rand( array_flip( range( 1, 99 )))); // set current
        $resultC = $asmit->current();
        $resultP = $asmit->pKeyGet( $asmit->getCurrentPkey());
        $this->assertEquals(
            [ $asmit->key() => $resultC ],
            $resultP,
            'test25-1, exp : ' . $resultC . ', got ' . reset( $resultP )
        );

        $key = 'TEST';
        for( $x = 1; $x <= 5; $x++ ) {
            $asmit->setCurrentPkey( $key . $x );
            $resultX = $asmit->pKeyGet( $key . $x );
            $this->assertEquals(
                $resultP,
                $resultX,
                'test25-2-' . $x . ', exp : ' . reset( $resultP ) . ', got ' . reset( $resultX )
            );
        } // end for
        $asmit = null;
    }

    /**
     * Test Asit pKeySeek + InvalidArgumentException
     *
     * @test
     */
    public function asitTest28() : void
    {
        $asit = Asit::factory( [ 'key1' => 'value' ] );
        $ok = 0;
        try {
            $asit->pKeySeek( 'key28' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test28, exp 2, got ' . $ok );
    }

    /**
     * Testing Asit/Asittag 'current' methods :
     *    getCurrentPkey, setCurrentPkey,
     *
     * @test
     */
    public function asitTest30() : void
    {

        foreach( [ new Asit(), new Asittag() ] as $aIx => $asit ) {
            foreach( $this->arrayLoader( 100 ) as $key => $value ) {
                $asit->append( $value );
            } // end for
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                'test301-' . $aIx . ', exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
            );

            $asit->seek( array_rand( array_flip( range( 1, 88 ) ) ) ); // set current
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                'test302-' . $aIx . 'exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
            );

            $asit = null;
        } // end foreach

    }

    /**
     * Testing Asit 'current' methods :
     *    getCurrentPkey, setCurrentPkey,
     *
     * @test
     */
    public function asitTest31() : void
    {

        $asit = new Asit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $asit->rewind()->previous();
        $ok = 0;
        try {
            $asit->getCurrentPkey(); // no current exists
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test311, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $asit->setCurrentPkey( 'fake' ); // no current exists
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test312, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 31, 39 )))); // set current

        $pKey1 = $asit->getCurrentPkey();
        $this->assertEquals(
            'key' . $asit->key(),
            $asit->getCurrentPkey(),
            'test31'
        );

        $pKey1  = $asit->getCurrentPkey();
        $asit->next();

        $ok = 0;
        try {
            $asit->setCurrentPkey( $pKey1 ); // try to set some others pKey
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 3;
        }
        catch( Exception $e ) {
            $ok = 4;
        }
        $this->assertEquals( 3, $ok, 'test314, exp 3, got ' . $ok );

        $index1  = $asit->key();
        $newPkey = 'newPkey';
        $asit->setCurrentPkey( $newPkey );
        $this->assertEquals(
            $newPkey,
            $asit->getCurrentPkey(),
            'test315'
        );
        $this->assertEquals(
            $index1,
            $asit->key(),
            'test316'
        );
        $this->assertTrue(
            $asit->pKeyExists( $pKey1 ),
            'test317'
        );

        $asit = null;
    }

    /**
     * Testing other primary key methods
     *
     * @test
     */
    public function asitTest33() : void
    {

        $element = 'element';
        $asit = new Asit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for
        $asit->seek( array_rand( array_flip( range( 30, 34 ))));

        $pKey1 = $asit->getCurrentPkey();
        $ix1   = $asit->key();

        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( $pKey1 ),  // test get( $pKey )
            'test33-1'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( [ $pKey1 ] ),  // test get( [ $pKey ] )
            'test33-3'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( [ $pKey1, 'noExists' ] ),  // test get( [ $pKey, 'noExists' ] )
            'test33-5'
        );
        $this->assertEquals(
            [], $asit->pKeyGet( [ 'noExists1', 'noExists2' ] ),  // test get( [ 'noExists1', 'noExists2' ] ) i.e. pKey not found
            'test33-6'
        );

        $asit->seek( array_rand( array_flip( range( 35, 39 ))));
        $pKey2 = $asit->GetCurrentPkey();
        $ix2   = $asit->key();
        $search = [ $pKey1, $pKey2 ];
        $this->assertCount(
            2,
            $asit->pKeyGet( $search ),
            'test33-8'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1, $ix2 => 'element' . $ix2 ], $asit->pkeyGet( $search ),  // pkeyGet, alias of get()
            'test33-9 : '
        );

        $asit = null;
    }

    /**
     * Testing Asit - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest34() : void
    {

        $asit = new Asit();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                (( 0 === ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx
            );
        } // end for

        $this->assertCount(
            $asit->count(), $asit->getPkeys(), 'test34-1'
        );

        foreach( $asit->getPkeys( SORT_FLAG_CASE | SORT_STRING ) as $pIx => $pKey ) {
            // case-insensitively sort
            $exp = ( 0 === ( $pIx % 2 )) ? 'KEY' :  'key';
//          echo ' 1 got : ' . $pKey . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $pKey, 0, 3 ),
                'test34-2-' . $pIx . ', exp : ' . $exp . ', got : ' . $pKey
            );
        }
        foreach( $asit->getPkeys() as $pIx => $pKey ) {
            // case-sensitively sort
            $exp = ( $pIx < 5 ) ? 'KEY' : 'key';
//          echo ' 2 got : ' . $pKey . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $pKey, 0, 3 ),
                'test34-3-' . $pIx . ', exp : ' . $exp . ', got : ' . $pKey
            );
        }
        $asit = null;
    }

    /**
     * Testing Asit/Asittag/AsittagList setCurrentPkey
     *
     * @test
     */
    public function asitTest35() : void
    {

        $asit = new AsittagList();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $asit->seek( array_rand( array_flip( range( 30, 39 ))));
        $pKey1 = $asit->getCurrentPkey();

        $newKey = 'otherKey';
        $asit->setCurrentPkey( $newKey );
        $this->assertTrue(  $asit->pKeyExists( $newKey ),'test35-1' );
        $this->assertFalse( $asit->pKeyExists( $pKey1 ), 'test35-2' );
        $asit->setCurrentPkey( $newKey );
        $this->assertTrue(  $asit->pKeyExists( $newKey ),'test35-3' );
        $asit->setCurrentPkey( $pKey1 );
        $this->assertTrue(  $asit->pKeyExists( $pKey1 ),'test35-4' );
        $this->assertFalse( $asit->pKeyExists( $newKey ), 'test35-5' );

        $asit = null;
    }

    /**
     * Testing Asmit/Asmittag/AsmittagList setCurrentPkey + countPkey
     *
     * @test
     */
    public function asitTest36() : void
    {

        $asit = new AsmittagList();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $asit->seek( array_rand( array_flip( range( 0, 99 ))));
        $pKey1 = $asit->getCurrentPkey();
        $this->assertTrue( $asit->pKeyExists( $pKey1 ), 'test36-1' );

        $result = $asit->current();

        $newKey1 = 'otherKey1';
        $asit->setCurrentPkey( $newKey1 );
        $this->assertTrue( $asit->pKeyExists( $newKey1 ),'test36-2' );
        $asit->setCurrentPkey( $newKey1 );
        $this->assertTrue( $asit->pKeyExists( $newKey1 ),'test36-3' );
        $this->assertEquals( 2, $asit->countPkey( $newKey1 ), 'test36-4' );

        $newKey2 = 'otherKey2';
        $asit->setCurrentPkey( $newKey2 );
        $this->assertTrue( $asit->pKeyExists( $newKey2 ),'test36-5' );
        $this->assertEquals( 3, $asit->countPkey( $newKey2 ), 'test36-6' );

        $asit->rewind()->previous()->previous()->previous(); // make current invalid
        $asit->pKeySeek( $newKey2 );
        $this->assertEquals(
            $result,
            $asit->current(),
            'test26-7'
        );

        $asit = null;
    }

    /**
     * Testing Asittag setCurrentPkey + out of pos
     *
     * @test
     */
    public function asitTest38() : void
    {
        $asit    = new Asittag( $this->arrayLoader( 10));
        $newPkey = 'testbcdefg';
        $ok = 0;
        try {
            $asit->last()->next()->next()->next()->setCurrentPkey( $newPkey );
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test38-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag found setPkey + exception (org not found)
     *
     * @test
     */
    public function asitTest39() : void
    {
        $asit      = new Asittag( $this->arrayLoader( 10));
        $otherPkey = 'testbcdefg';
        $ok = 0;
        try {
            $asit->replacePkey( 'notFound', $otherPkey );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test39-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag found orgPkey + exception, but otherPkey exists
     *
     * @test
     */
    public function asitTest40() : void
    {
        $asit      = new Asittag( $this->arrayLoader( 10));
        $asit->rewind();
        $onePkey   = $asit->getCurrentPkey();
        $otherPkey = $asit->next()->next()->next()->next()->getCurrentPkey();

        $ok = 0;
        try {
            $asit->replacePkey( $onePkey, $otherPkey );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test40-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag setCurrentPkey + dupl pKey (i.e. key exists)
     *
     * @test
     */
    public function asitTest41() : void
    {
        $asit     = new Asittag( $this->arrayLoader( 10));
        $duplPkey = $asit->last()->previous()->previous()->previous()->getCurrentPkey();
        $asit->previous()->previous()->previous()->previous(); // set another current
        $ok = 0;
        try {
            $asit->setCurrentPkey( $duplPkey );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test37-26, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest61() : void
    {

        $asit = new Asittag();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                (( 0 === ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx
            );
        } // end for

        $this->assertCount(
            $asit->count(), $asit->getPkeys(), 'test61-1'
        );

        $asit = null;
    }

    /**
     * Testing Asittag get - no found pKey
     *
     * @test
     */
    public function asitTest73() : void
    {
        $data = $this->arrayLoader( 100 );
        $asit = new Asittag();
        foreach( $data as $element ) {
            $asit->append( $element );
        }
        $this->assertEquals(
            [],
            $asit->pKeyTagGet( 'fakePkey' ),
            'test73-1'
        );
        $this->assertEquals(
            array_values( $data ),
            $asit->pKeyTagGet(),
            'test73-2'
        );

        $asit = null;
    }

    /**
     * Test Asit / Asittag append Pkey + InvalidArgumentException, duplicate + invalid pKey
     *
     * @test
     */
    public function asitTest81() : void
    {
        $data = [ 'key' => 'value' ];
        foreach( [ Asit::factory( $data ), Asittag::factory( $data ) ] as $aIx => $asit ) {
            $ok   = 0;
            try {
                $asit->append( 'value2', 'key' );  // duplicate pKey
                $ok = 1;
            }
            catch( InvalidArgumentException $e ) {
                $ok = 2;
            }
            catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertEquals(
                2, $ok, 'test81-1-' . $aIx . ', exp 2, got ' . $ok  . ' ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );

            $ok = 0;
            try {
                $asit->append( 'value2', [ 1, 2, 3 ] ); // unvalid key
                $ok = 1;
            }
            catch( InvalidArgumentException $e ) {
                $ok = 2;
            }
            catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertEquals(
                2, $ok, 'test81-2-' . $aIx . ', exp 2, got ' . $ok  . ' ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );

            $asit = null;
        } // end foreach

    }

    /**
     * Test Asit getCurrentPkey + RuntimeException
     *
     * @test
     */
    public function asitTest9() : void
    {
        $asit = Asit::factory( [ 1 => 'value' ] );
        $asit->next();
        $ok = 0;
        try {
            $asit->getCurrentPkey();
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, 'test9-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Test get, no args
     *
     * @test
     */
    public function asitTest10() : void
    {
        $asit = new Asit();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for
        $this->assertSameSize(
            $asit->getPkeys(),
            $asit->get(),
            'test 10-1'
        );

        $asit = null;
    }

    /**
     * Test AsitList::get with sort
     *
     * @test
     */
    public function asitTest11() : void
    {
        $asit    = new AsitList( $this->arrayLoader( 10 ), AsitList::STRING );
        $result  = $asit->pKeyGet( null, [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'element9',
            $result1,
            'test 11-1 exp "element9", got : ' . $result1
        );

        $asit = null;
    }

    /**
     * Test AsittagList::get with sort
     *
     * @test
     */
    public function asittagTest12() : void
    {
        $asit    = new AsittagList( $this->arrayLoader( 10 ), AsitList::STRING );
        $result  = $asit->pKeyTagGet( null, null, null, null, [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'element9',
            $result1,
            'test 12-1 exp "element9", got : ' . $result1
        );

        $asit = null;
    }

    public static function cmp( $a, $b ) : int
    {
        if( $a === $b ) {
            return 0;
        }
        return ( $a > $b ) ? -1 : +1;
    }
}
