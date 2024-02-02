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

use Exception;
use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\PositionException;
use RuntimeException;
use Traversable;

class Asit1Test extends AsitBaseTest
{
    /**
     * @test Asit exists, count,
     *
     */
    public function asitTest101() : void
    {
        $asit = new Asit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $this->assertEquals( 100, $asit->count(), __FUNCTION__ . ' #1' );
        $this->assertTrue(  $asit->exists( 0 ), __FUNCTION__ . ' #2' );
        $this->assertFalse( $asit->exists( -1 ), __FUNCTION__ . ' #3' );
        $this->assertFalse( $asit->exists( -1111 ), __FUNCTION__ . ' #4' );
        $this->assertTrue(  $asit->exists( 99 ), __FUNCTION__ . ' #5' );
        $this->assertFalse( $asit->exists( 100 ), __FUNCTION__ . ' #6' );
        $this->assertFalse( $asit->exists( 100000 ), __FUNCTION__ . ' #7' );
        $asit = null;
    }

    /**
     * Testing Asit Iterator methods excl GetIterator - Traversable
     *
     * @test
     */
    public function asitTest102() : void
    {

        $asit = new Asit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $asit->rewind();               // test rewind
        $this->assertEquals(
            0, $asit->key(), __FUNCTION__ . ' #1'
        );
        $this->assertEquals(
            'element0', $asit->current(), __FUNCTION__ . ' #2'
        );
        $pKey = $asit->getCurrentPkey();
        $this->assertEquals(
            'key0', $pKey, __FUNCTION__ . ' #3'
        );

        $asit->next();             // test next
        $this->assertEquals(
            1, $asit->key(), __FUNCTION__ . ' #4'
        );
        $this->assertEquals(
            'element1', $asit->current(), __FUNCTION__ . ' #5'
        );
        $this->assertEquals(
            'key1', $asit->getCurrentPkey(), __FUNCTION__ . ' #6'
        );

        $asit->last();           // test last
        $this->assertEquals(
            99, $asit->key(), __FUNCTION__ . ' #7'
        );
        $this->assertEquals(
            'element99', $asit->current(), __FUNCTION__ . ' #8'
        );
        $this->assertEquals(
            'key99', $asit->getCurrentPkey(), __FUNCTION__ . ' #9'
        );

        $asit->previous();    // test previous
        $this->assertEquals(
            98, $asit->key(), __FUNCTION__ . ' #10'
        );
        $this->assertEquals(
            'element98', $asit->current(), __FUNCTION__ . ' #11'
        );
        $this->assertEquals(
            'key98', $asit->getCurrentPkey(), __FUNCTION__ . ' #12'
        );

        $asit->last();
        $asit->next();
        $this->assertEquals(
            100, $asit->key(), __FUNCTION__ . ' #13'
        );
        $this->assertFalse( $asit->valid(), __FUNCTION__ . ' #14' );

        $asit->rewind();
        $asit->previous();
        $this->assertEquals(-1, $asit->key(), __FUNCTION__ . ' #15' );
        $this->assertFalse( $asit->valid(), __FUNCTION__ . ' #16' );

        $asit->seek( 0 );   // test seek
        $this->assertEquals(0, $asit->key(), __FUNCTION__ . ' #17' );
        $this->assertEquals( 'element0', $asit->current(), __FUNCTION__ . ' #18' );
        $this->assertEquals( 'key0', $asit->getCurrentPkey(), __FUNCTION__ . ' #19' );

        $asit->seek( 50 );
        $this->assertEquals( 50, $asit->key(), __FUNCTION__ . ' #20' );
        $this->assertEquals( 'element50', $asit->current(), __FUNCTION__ . ' #21'
        );
        $this->assertEquals('key50', $asit->getCurrentPkey(), __FUNCTION__ . ' #22' );

        $asit = null;
    }

    /**
     * Testing Asit/Asmit IteratorAggregate interface - Traversable
     *     method GetIterator + getPkeyIterator
     *
     *
     * @test
     */
    public function asitTest103() : void
    {

        foreach( [ new Asit(), new Asmit() ] as $asit ) {

            foreach( self::arrayLoader( 100 ) as $key => $value ) {
                $asit->append( $value, $key );
            }

            $this->assertTrue(
                ( $asit->GetIterator() instanceof Traversable ),   // test GetIterator - Traversable
                __FUNCTION__ . ' #1'
            );
            $this->assertTrue(
                ( $asit instanceof Traversable ),  // test Asit - Traversable
                __FUNCTION__ . ' #2'
            );

            // testing Traversable, i.e. makes the class traversable using foreach
            $cnt = $key = 0;
            $value = null;
            foreach( $asit as $key => $value ) { // 'internal key', NOT pKey
                ++$cnt;
            }
            $this->assertEquals(
                100, $cnt, __FUNCTION__ . ' #3'
            );
            $this->assertEquals(
                99, $key, __FUNCTION__ . ' #4'
            );
            $this->assertEquals(
                'element99', $value, __FUNCTION__ . ' #5'
            );
            $asit->seek( $key ); // position iterator last
            $this->assertEquals(
                'key99', $asit->getCurrentPkey(), __FUNCTION__ . ' #6, exp: key99, got: ' . $asit->getCurrentPkey()
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
                        $asit->pKeySeek( $pKey )->current(),   // test pKeySeek
                        __FUNCTION__ . ' #12 (' . $cnt . ')'
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
    public function asmitTest104() : void
    {
        // invalid key
        try {
            Asmit::assertPkey( '' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        // don't exist
        try {
            Asmit::factory()->pKeySeek( 'key23' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #2, exp 2, got ' . $ok );

        // don't exist
        try {
            Asmit::factory()->countPkey( 'key23' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #3, exp 2, got ' . $ok );

        // remove but don't exist
        try {
            Asmit::factory( self::arrayLoader( 10 ))->removePkey( 'test23-3' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #4, exp 2, got ' . $ok );

        // no current
        try {
            Asmit::factory()->getCurrentPkey();
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #5, exp 2, got ' . $ok );

        // set the same primary key again, allowed
        $asmit = Asmit::factory()->append( 'value', 'key' );
        $pKey  = $asmit->getCurrentPkey();
        try {
            $asmit->setCurrentPkey( $pKey );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {  //  PkeyException | PositionException
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 1, $ok, __FUNCTION__ . ' #6, exp 1, got ' . $ok );

        // set another key for value1 BUT same as for value2, exception exp
        $asmit = Asmit::factory()
            ->append( 'value1', 'key1' )
            ->append( 'value2', 'key2' );
        $asmit->rewind(); // current is 1
        try {
            $asmit->setCurrentPkey( 'key2' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #7, exp 2, got ' . $ok );
    }

    /**
     * Test Asmit addCurrentPkey/countPkey/removePkey/getCurrentPkey
     *
     * @test
     */
    public function asmitTest105() : void
    {
        $asmit = new Asmit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asmit->append( $value, $key );
        }

        $asmit->seek( array_rand( array_flip( range( 2, 92 )))); // set random current

        $pKey1 = $asmit->getCurrentPkey();
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey1 ),
            __FUNCTION__ . ' #1, exp 1, got ' . $asmit->countPkey( $pKey1 )
        );

        $asmit->removePkey( $pKey1 );
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey1 ),
            __FUNCTION__ . ' #2, exp 1, got ' . $asmit->countPkey( $pKey1 )
        );

        $pKey2 = __FUNCTION__ . 2;
        $asmit->addCurrentPkey( $pKey2 );
        $this->assertEquals(
            2,
            $asmit->countPkey( $pKey2 ),
            __FUNCTION__ . ' #3, exp 2, got ' . $asmit->countPkey( $pKey2 )
        );

        $asmit->removePkey( $pKey1 );
        $this->assertEquals(
            1,
            $asmit->countPkey( $pKey2 ),  // <-------
            __FUNCTION__ . ' #4, exp 1, got ' . $asmit->countPkey( $pKey2 )
        );
        $this->assertFalse(
            $asmit->pKeyExists( $pKey1 ),
            __FUNCTION__ . ' #5, found : ' . $pKey1
        );

        $pKey3 = __FUNCTION__ . 3;
        $asmit->addCurrentPkey( $pKey3 );
        $exp   = [ $pKey2, $pKey3 ];
        $pKeys = $asmit->getCurrentPkeys();
        $this->assertEquals(
            $exp,
            $pKeys,
            __FUNCTION__ . ' #6, exp : ' . implode( ',', $exp ) . ',  got : ' . implode( ',', $pKeys )
        );

        $iterator = $asmit->getPkeyIterator();
        $this->assertTrue(
            ( $iterator instanceof Traversable ),   // test getPkeyIterator - Traversable
            __FUNCTION__ . ' #7'
        );
        $this->assertEquals(
            100,
            $iterator->count(),
            __FUNCTION__ . ' #8, exp : 100,  got : ' . $iterator->count()
        );

        $pKeys = [];
        foreach( $iterator as $key => $value ) {
            $pKeys[] = $key;
        }
        $this->assertContains( $pKey2, $pKeys, __FUNCTION__ . ' #9 exp ' . $pKey2 . ' in ' . implode( ', ', $pKeys ));

        $asmit = null;
    }

    /**
     * Test Asmit get
     *
     * @test
     */
    public function asmitTest106() : void
    {
        $asmit = new Asmit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asmit->append( $value, $key );
        }

        $asmit->seek( array_rand( array_flip( range( 1, 99 )))); // set current
        $resultC = $asmit->current();
        $resultP = $asmit->pKeyGet( $asmit->getCurrentPkeys());
        $this->assertEquals(
            [ $asmit->key() => $resultC ],
            $resultP,
            __FUNCTION__ . ' #1, exp : ' . $resultC . ', got ' . reset( $resultP )
        );

        $key = 'TEST';
        for( $x = 1; $x <= 5; $x++ ) {
            $asmit->setCurrentPkey( $key . $x );
            $resultX = $asmit->pKeyGet( $key . $x );
            $this->assertEquals(
                $resultP,
                $resultX,
                __FUNCTION__ . ' #2-' . $x . ', exp : ' . reset( $resultP ) . ', got ' . reset( $resultX )
            );
        } // end for
        $asmit = null;
    }

    /**
     * Test Asit pKeySeek + InvalidArgumentException
     *
     * @test
     */
    public function asitTest107() : void
    {
        $asit = Asit::factory( [ 'key1' => 'value' ] );
        $ok = 0;
        try {
            $asit->pKeySeek( 'key28' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );
    }

    /**
     * Testing Asit/Asittag 'current' methods :
     *    getCurrentPkey, setCurrentPkey,
     *
     * @test
     */
    public function asitTest108() : void
    {

        foreach( [ new Asit(), new Asittag() ] as $aIx => $asit ) {
            foreach( self::arrayLoader( 100 ) as $key => $value ) {
                $asit->append( $value );
            } // end for
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                __FUNCTION__ . ' #1-' . $aIx . ', exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
            );

            $asit->seek( array_rand( array_flip( range( 1, 88 ) ) ) ); // set current
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                __FUNCTION__ . ' #2-' . $aIx . 'exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
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
    public function asitTest109() : void
    {

        $asit = new Asit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $asit->rewind()->previous();
        $ok = 0;
        try {
            $asit->getCurrentPkey(); // no current exists
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $asit->setCurrentPkey( 'fake' ); // no current exists
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #2, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 31, 39 )))); // set current

        $pKey1 = $asit->getCurrentPkey();
        $this->assertEquals(
            'key' . $asit->key(),
            $asit->getCurrentPkey(),
            __FUNCTION__ . ' #3'
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
        catch( PkeyException $e ) {
            $ok = 3;
        }
        catch( Exception $e ) {
            $ok = 4;
        }
        $this->assertEquals( 3, $ok, __FUNCTION__ . ' #4, exp 3, got ' . $ok );

        $index1  = $asit->key();
        $newPkey = 'newPkey';
        $asit->setCurrentPkey( $newPkey );
        $this->assertEquals(
            $newPkey,
            $asit->getCurrentPkey(),
            __FUNCTION__ . ' #5'
        );
        $this->assertEquals(
            $index1,
            $asit->key(),
            __FUNCTION__ . ' #6'
        );
        $this->assertTrue(
            $asit->pKeyExists( $pKey1 ),
            __FUNCTION__ . ' #7'
        );

        $asit = null;
    }

    /**
     * Testing other primary key methods
     *
     * @test
     */
    public function asitTest110() : void
    {
        $element = 'element';
        $asit = new Asit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for
        $asit->seek( array_rand( array_flip( range( 30, 34 ))));

        $pKey1 = $asit->getCurrentPkey();
        $ix1   = $asit->key();

        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( $pKey1 ),  // test get( $pKey )
            __FUNCTION__ . ' #1'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( [ $pKey1 ] ),  // test get( [ $pKey ] )
            __FUNCTION__ . ' #3'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( [ $pKey1, 'noExists' ] ),  // test get( [ $pKey, 'noExists' ] )
            __FUNCTION__ . ' #5'
        );
        $this->assertEquals(
            [], $asit->pKeyGet( [ 'noExists1', 'noExists2' ] ),  // test get( [ 'noExists1', 'noExists2' ] ) i.e. pKey not found
            __FUNCTION__ . ' #6'
        );

        $asit->seek( array_rand( array_flip( range( 35, 39 ))));
        $pKey2 = $asit->GetCurrentPkey();
        $ix2   = $asit->key();
        $search = [ $pKey1, $pKey2 ];
        $this->assertCount(
            2,
            $asit->pKeyGet( $search ),
            __FUNCTION__ . ' #8'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1, $ix2 => 'element' . $ix2 ], $asit->pkeyGet( $search ),  // pkeyGet, alias of get()
            __FUNCTION__ . ' #9 : '
        );

        $asit = null;
    }

    /**
     * Testing Asit - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest111() : void
    {
        $asit = new Asit();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $pKey = (( 0 === ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx;
            $asit->append( 'element', $pKey );
//          echo ' 0 set (' . $pIx . ') : ' . $pKey . PHP_EOL; // test ###
        } // end for

        $this->assertCount(
            $asit->count(), $asit->getPkeys(), __FUNCTION__ . ' #1'
        );

        $pKeys = $asit->getPkeys( null, SORT_FLAG_CASE | SORT_STRING );
//      echo 'pKeys : ' . implode( ', ', $pKeys ) . PHP_EOL; // test ###
        $pIx = -1;
        foreach( $pKeys as $pKey ) { // will now have new keys
            ++$pIx;
            // case-insensitively sort
            $exp = ( 0 === ( $pIx % 2 )) ? 'KEY' :  'key';
//          echo ' 1 got (' . $pIx . ') : ' . $pKey . PHP_EOL; // test ###
            $act = substr( $pKey, 0, 3 );
            $this->assertEquals(
                $exp,
                $act,
                __FUNCTION__ . ' #2-' . $pIx . ', exp : ' . $exp . ', got : ' . $act . ' for ' . $pKey
            );
        }
        $pKeys = $asit->getPkeys();
        $pIx = -1;
        foreach( $pKeys as $pKey ) {
            ++$pIx;
            // case-sensitively sort
            $exp = ( $pIx < 5 ) ? 'KEY' : 'key';
//          echo ' 2 got (' . $pIx . ') : ' . $pKey . PHP_EOL; // test ###
            $this->assertEquals(
                $exp,
                substr( $pKey, 0, 3 ),
                __FUNCTION__ . ' #3-' . $pIx . ', exp : ' . $exp . ', got : ' . $pKey
            );
        }
        $asit = null;
    }

    /**
     * Testing Asit/Asittag/AsittagList setCurrentPkey
     *
     * @test
     */
    public function asitTest112() : void
    {
        $asit = new AsittagList( AsittagList::STRING );
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $asit->seek( array_rand( array_flip( range( 30, 39 ))));
        $pKey1 = $asit->getCurrentPkey();

        $newKey = 'otherKey';
        $asit->setCurrentPkey( $newKey );
        $this->assertTrue(  $asit->pKeyExists( $newKey ),__FUNCTION__ . ' #1' );
        $this->assertFalse( $asit->pKeyExists( $pKey1 ), __FUNCTION__ . ' #2' );
        $asit->setCurrentPkey( $newKey );
        $this->assertTrue(  $asit->pKeyExists( $newKey ),__FUNCTION__ . ' #3' );
        $asit->setCurrentPkey( $pKey1 );
        $this->assertTrue(  $asit->pKeyExists( $pKey1 ),__FUNCTION__ . ' #4' );
        $this->assertFalse( $asit->pKeyExists( $newKey ), __FUNCTION__ . ' #5' );

        $asit = null;
    }

    /**
     * Testing Asmit/Asmittag/AsmittagList setCurrentPkey + countPkey
     *
     * @test
     */
    public function asitTest113() : void
    {
        $asit = new AsmittagList( AsmittagList::STRING );
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $asit->seek( array_rand( array_flip( range( 0, 99 ))));
        $pKey1 = $asit->getCurrentPkey();
        $this->assertTrue( $asit->pKeyExists( $pKey1 ), __FUNCTION__ . ' #1' );

        $result = $asit->current();

        $newKey1 = 'otherKey1';
        $asit->setCurrentPkey( $newKey1 );
        $this->assertTrue( $asit->pKeyExists( $newKey1 ),__FUNCTION__ . ' #2' );
        $asit->setCurrentPkey( $newKey1 );
        $this->assertTrue( $asit->pKeyExists( $newKey1 ),__FUNCTION__ . ' #3' );
        $this->assertEquals( 2, $asit->countPkey( $newKey1 ), __FUNCTION__ . ' #4' );

        $newKey2 = 'otherKey2';
        $asit->setCurrentPkey( $newKey2 );
        $this->assertTrue( $asit->pKeyExists( $newKey2 ),__FUNCTION__ . ' #5' );
        $this->assertEquals( 3, $asit->countPkey( $newKey2 ), __FUNCTION__ . ' #6' );

        $asit->rewind()->previous()->previous()->previous(); // make current invalid

        $this->assertEquals(
            $result,
            $asit->pKeySeek( $newKey2 )->current(),
            __FUNCTION__ . ' #7'
        );

        $asit = null;
    }

    /**
     * Testing Asittag setCurrentPkey + out of pos
     *
     * @test
     */
    public function asitTest114() : void
    {
        $asit    = new Asittag( self::arrayLoader( 10));
        $newPkey = 'testbcdefg';
        $ok = 0;
        try {
            $asit->last()->next()->next()->next()->setCurrentPkey( $newPkey );
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag found setPkey + exception (org not found)
     *
     * @test
     */
    public function asitTest115() : void
    {
        $asit      = new Asittag( self::arrayLoader( 10));
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
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag found orgPkey + exception, but otherPkey exists
     *
     * @test
     */
    public function asitTest116() : void
    {
        $asit      = new Asittag( self::arrayLoader( 10));
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
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag setCurrentPkey + dupl pKey (i.e. key exists)
     *
     * @test
     */
    public function asitTest117() : void
    {
        $asit     = new Asittag( self::arrayLoader( 10));
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
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest118() : void
    {
        $asit = new Asittag();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                (( 0 === ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx
            );
        } // end for

        $this->assertCount(
            $asit->count(), $asit->getPkeys(), __FUNCTION__ . ' #1'
        );

        $asit = null;
    }

    /**
     * Testing Asittag get - no found pKey
     *
     * @test
     */
    public function asitTest119() : void
    {
        $data = self::arrayLoader( 100 );
        $asit = new Asittag();
        foreach( $data as $element ) {
            $asit->append( $element );
        }
        $this->assertEquals(
            [],
            $asit->pKeyTagGet( 'fakePkey' ),
            __FUNCTION__ . ' #1'
        );
        $this->assertEquals(
            array_values( $data ),
            $asit->pKeyTagGet(),
            __FUNCTION__ . ' #2'
        );

        $asit = null;
    }

    /**
     * Test Asit / Asittag append Pkey + InvalidArgumentException, duplicate + invalid pKey
     *
     * @test
     */
    public function asitTest120() : void
    {
        $data = [ 'key' => 'value' ];
        foreach( [ Asit::factory( $data ), Asittag::factory( $data ) ] as $aIx => $asit ) {
            $ok   = 0;
            try {
                $asit->append( 'value2', 'key' );  // duplicate pKey
                $ok = 1;
            }
            catch( PkeyException $e ) {
                $ok = 2;
            }
            catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertEquals(
                2, $ok, __FUNCTION__ . ' #1-' . $aIx . ', exp 2, got ' . $ok  . ' ' . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
            );

            $asit = null;
        } // end foreach

    }

    /**
     * Test Asit getCurrentPkey + PkeyException
     *
     * @test
     */
    public function asitTest121() : void
    {
        $asit = Asit::factory( [ 1 => 'value' ] );
        $asit->next();
        $ok = 0;
        try {
            $asit->getCurrentPkey();
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Test get, no args
     *
     * @test
     */
    public function asitTest122() : void
    {
        $asit = new Asit();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
        } // end for
        $this->assertSameSize(
            $asit->getPkeys(),
            $asit->get(),
            __FUNCTION__ . ' #1'
        );

        $asit = null;
    }

    /**
     * Test AsitList::get with sort
     *
     * @test
     */
    public function asitTest123() : void
    {
        $asit    = new AsitList( self::arrayLoader( 10 ), AsitList::STRING );
        $result  = $asit->pKeyGet( null, [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'element9',
            $result1,
            __FUNCTION__ . ' #1 exp "element9", got : ' . $result1
        );

        $asit = null;
    }

    /**
     * Test AsittagList::get with sort
     *
     * @test
     */
    public function asittagTest124() : void
    {
        $asit    = new AsittagList( self::arrayLoader( 10 ), AsitList::STRING );
        $result  = $asit->pKeyTagGet( null, null, null, null, [ self::class, 'cmp' ] );
        $result1 = reset( $result );
        $this->assertEquals(
            'element9',
            $result1,
            __FUNCTION__ . ' #1 exp "element9", got : ' . $result1
        );

        $asit = null;
    }

    /**
     * @param int|string $a
     * @param int|string $b
     * @return int
     */
    public static function cmp( int|string $a, int|string $b ) : int
    {
        if( $a === $b ) {
            return 0;
        }
        return ( $a > $b ) ? -1 : +1;
    }
}
