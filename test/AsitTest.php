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
use InvalidArgumentException;
use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Traversable;

class AsitTest extends TestCase
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
     * @test Asit exists, count,
     *
     */
    public function asitTest1() {
        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $this->assertTrue(
            ( 1000 == $asit->count()),
            'test11'
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
            $asit->exists( 999 ),
            'test15'
        );
        $this->assertFalse(
            $asit->exists( 1000 ),
            'test16'
        );
        $this->assertFalse(
            $asit->exists( 100000 ),
            'test7'
        );
    }

    /**
     * Testing Asit Iterator methods excl GetIterator - Traversable
     *
     * @test
     */
    public function asitTest21() {

        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

        $asit->rewind();               // test rewind
        $this->assertTrue(
            0 == $asit->key(),
            'test21-1'
        );
        $this->assertTrue(
            'element0' == $asit->current(),
            'test21-2'
        );
        $pKey = $asit->getCurrentPkey();
        $this->assertTrue(
            'key0' == $pKey,
            'test21-3'
        );

        $asit->next();             // test next
        $this->assertTrue(
            1 == $asit->key(),
            'test21-4'
        );
        $this->assertTrue(
            'element1' == $asit->current(),
            'test21-5'
        );
        $this->assertTrue(
            'key1' == $asit->getCurrentPkey(),
            'test21-6'
        );

        $asit->last();           // test last
        $this->assertTrue(
            999 == $asit->key(),
            'test21-7'
        );
        $this->assertTrue(
            'element999' == $asit->current(),
            'test21-8'
        );
        $this->assertTrue(
            'key999' == $asit->getCurrentPkey(),
            'test21-9'
        );

        $asit->previous();    // test previous
        $this->assertTrue(
            998 == $asit->key(),
            'test21-10'
        );
        $this->assertTrue(
            'element998' == $asit->current(),
            'test21-11'
        );
        $this->assertTrue(
            'key998' == $asit->getCurrentPkey(),
            'test21-12'
        );

        $asit->last();
        $asit->next();
        $this->assertTrue(
            1000 == $asit->key(),
            'test21-13'
        );
        $this->assertFalse(
            $asit->valid(),
            'test21-14'
        );

        $asit->rewind();
        $asit->previous();
        $this->assertTrue(
            -1 == $asit->key(),
            'test21-15'
        );
        $this->assertFalse(
            $asit->valid(),
            'test21-16'
        );

        $asit->seek( 0 );   // test seek
        $this->assertTrue(
            0 == $asit->key(),
            'test21-17'
        );
        $this->assertTrue(
            'element0' == $asit->current(),
            'test21-18'
        );
        $this->assertTrue(
            'key0' == $asit->getCurrentPkey(),
            'test21-19'
        );

        $asit->seek( 50 );
        $this->assertTrue(
            50 == $asit->key(),
            'test21-20'
        );
        $this->assertTrue(
            'element50' == $asit->current(),
            'test21-21'
        );
        $this->assertTrue(
            'key50' == $asit->getCurrentPkey(),
            'test21-22'
        );

    }

    /**
     * Testing Asit IteratorAggregate interface - Traversable
     *     method GetIterator + getPkeyIterator
     *
     *
     * @test
     */
    public function asitTest22() {

        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
        } // end for

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
        foreach(   $asit   as $key => $value ) { // 'internal key', NOT pKey
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
        $asit->seek( $key ); // position iterator last
        $this->assertTrue(
            ( 'key999' == $asit->getCurrentPkey()),
            'test22-6'
        );


        $this->assertTrue(
            ( $asit->GetPkeyIterator() instanceof Traversable ),   // test GetPkeyIterator - Traversable
            'test22-11'
        );
        $cnt = 0;
        foreach( $asit->GetPkeyIterator() as $pKey => $element ) {
            $cnt += 1;
            if( 0 == ( $cnt % 50 )) {
                $this->assertEquals(
                    $element,
                    $asit->pKeySeek( $pKey )->current(),   // test pKeySeek
                    'test22-12'
                );
            }
        }
    }

    /**
     * Test Asit seek + OutOfBoundsException
     *
     * @test
     */
    public function asitTest23() {
        $asit = Asit::factory( [ 1 => 'value' ] );
        $ok = 0;
        try {
            $asit->seek( 23 );
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

    /**
     * Test Asit pKeySeek + InvalidArgumentException
     *
     * @test
     */
    public function asitTest24() {
        $asit = Asit::factory( [ 'key1' => 'value' ] );
        $ok = 0;
        try {
            $asit->pKeySeek( 'key24' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test24, exp 2, got ' . $ok );
    }

    /**
     * Testing Asit/Asittag 'current' methods :
     *    getCurrentPkey, setCurrentPkey,
     *
     * @test
     *
     * @param Asit $asit
     */
    public function asitTest30() {

        foreach( [ new Asit(), new Asittag() ] as $aIx => $asit ) {
            foreach( $this->arrayLoader() as $key => $value ) {
                $asit->append( $value );
            } // end for
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                'test301-' . $aIx . ', exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
            );

            $asit->seek( array_rand( array_flip( range( 1, 888 ) ) ) ); // set current
            $this->assertEquals(
                $asit->key(),
                $asit->getCurrentPkey(),
                'test302-' . $aIx . 'exp ' . $asit->key() . ', got ' . $asit->getCurrentPkey()
            );

        } // end foreach

    }

    /**
     * Testing Asit 'current' methods :
     *    getCurrentPkey, setCurrentPkey,
     *
     * @test
     *
     * @param Asit $asit
     */
    public function asitTest31() {

        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
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
        $this->assertTrue( $ok == 2, 'test311, exp 2, got ' . $ok );

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
        $this->assertTrue( $ok == 2, 'test312, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 310, 319 )))); // set current

        $pKey1 = $asit->getCurrentPkey();
        $this->assertEquals(
            'key' . $asit->key(),
            $asit->getCurrentPkey(),
            'test313'
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
        $this->assertTrue( $ok == 3, 'test314, exp 3, got ' . $ok );

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
    }

    /**
     * Testing Asittag 'current' methods :
     *    getCurrentTags, hasCurrentTag, addCurrentTag,
     *
     * @test
     */
    public function asitTest32() {

        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $asit->rewind()->previous(); // no valid current

        $ok = 0;
        try {
            $asit->getCurrentTags(); // no current
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test321, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $asit->hasCurrentTag( 'fake '); // no current
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test322, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $asit->addCurrentTag( 'fake '); // no current
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test323, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 325, 329 )))); // set valid current

        $tags1 = $asit->getCurrentTags();
        $this->assertTrue(
            1 == count( $tags1 ),
            'test324 exp 1, got ' . implode( ',', $tags1 )
        );

        $newTag = 'newTag';
        $this->assertFalse(
            $asit->hasCurrentTag( $newTag ),
            'test325'
        );
        $asit->addCurrentTag( $newTag );
        $this->assertTrue(
            $asit->hasCurrentTag( $newTag ),
            'test326'
        );
        $this->assertTrue(
            1 == $asit->tagCount( $newTag ),
            'test327'
        );

        $tags2 = $asit->getCurrentTags();
        $this->assertTrue(
            2 == count( $tags2 ),
            'test328 exp 2, got ' . implode( ',', $tags2 )
        );
    }

    /**
     * Testing other primary key methods
     *
     * @test
     */
    public function asitTest33() {

        $element = 'element';
        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
        } // end for
        $asit->seek( array_rand( array_flip( range( 330, 335 ))));

        $pKey1 = $asit->getCurrentPkey();
        $ix1   = $asit->key();

        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->get( $pKey1 ),  // test get( $pKey )
            'test33-1'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pkeyGet( $pKey1 ),  // pkeyGet, alias of get()
            'test33-2'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->get( [ $pKey1 ] ),  // test get( [ $pKey ] )
            'test33-3'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->pKeyGet( [ $pKey1 ] ),  // pkeyGet, alias of get()
            'test33-4'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1 ], $asit->get( [ $pKey1, 'noExists' ] ),  // test get( [ $pKey, 'noExists' ] )
            'test33-5'
        );
        $this->assertEquals(
            [], $asit->get( [ 'noExists1', 'noExists2' ] ),  // test get( [ 'noExists1', 'noExists2' ] ) i.e. pKey not found
            'test33-6'
        );
        $this->assertEquals(
            [], $asit->pKeyGet( [ 'noExists1', 'noExists2' ] ),  // alias of above
            'test33-7'
        );

        $asit->seek( array_rand( array_flip( range( 336, 339 ))));
        $pKey2 = $asit->GetCurrentPkey();
        $ix2   = $asit->key();
        $search = [ $pKey1, $pKey2 ];
        $this->assertEquals(
            2, count( $asit->pKeyGet( $search )),
            'test33-8'
        );
        $this->assertEquals(
            [ $ix1 => $element . $ix1, $ix2 => 'element' . $ix2 ], $asit->pkeyGet( $search ),  // pkeyGet, alias of get()
            'test33-9 : '
        );

    }

    /**
     * Testing Asit - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest34() {

        $asit = new Asit();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                (( 0 == ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx
            );
        } // end for

        $this->assertTrue(
            $asit->count() == count( $asit->getPkeys()),
            'test34-1'
        );

        foreach( $asit->getPkeys( SORT_FLAG_CASE | SORT_STRING ) as $pIx => $pKey ) {
            // case-insensitively sort
            $exp = ( 0 == ( $pIx % 2 )) ? 'KEY' :  'key';
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
    }

    /**
     * Testing Asittag other primary key + tag methods
     *
     * @test
     */
    public function asitTest35() {

        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for

        $asit->seek( array_rand( array_flip( range( 351, 354 ))));
        $pKey1 = $asit->getCurrentPkey();
        $ix1   = $asit->key();

        $asit->seek( array_rand( array_flip( range( 355, 359 ))));
        $pKey2 = $asit->GetCurrentPkey();
        $ix2   = $asit->key();

        $search = [ $pKey1, $pKey2 ];

        $tags1 = $asit->getTags( $pKey1 );
        $this->assertEquals(
            [ $ix1 => 'element' . $ix1 ], $asit->get( $search, $tags1 ),  // get( pKeys, tags )
            'test35-10 search : ' . implode( ',', $search ) . ' tags : ' . implode( ',', $tags1 )
        );

        $this->assertEquals(
            [ $ix2 => 'element' . $ix2 ], $asit->get( $search, $asit->getTags( $pKey2 ) ),  // get( pKeys, tags )
            'test35-11'
        );
        $tags = array_merge(
            $asit->getTags( $pKey1 ),
            $asit->getTags( $pKey2 )
        );
        $this->assertEquals(
            [], $asit->get( $search, $tags, true ),  // get( pKeys, tags )
            'test35-12'
        );
        $this->assertEquals(
            [  $ix1 => 'element' . $ix1, $ix2 => 'element' . $ix2 ],
            $asit->get( $search, $tags, false ),  // get( pKeys, tags )
            'test35-13'
        );

        $asit->seek( array_rand( array_flip( range( 330, 334 ))));
        $otherPkey = $asit->getCurrentPkey();

        $asit->seek( array_rand( array_flip( range( 335, 339 ))));
        $orgPkey   = $asit->getCurrentPkey();
        $orgIndex  = $asit->key();
        // echo  'current ix :' . $asit->key() . ', pKey : ' . $orgPkey; // test ###

        $current   = $asit->current();
        $newTag    = 'someOtherColor';
        $asit->addCurrentTag( $newTag );
        $this->assertTrue(
            in_array( $newTag, $asit->getCurrentTags()),  // getCurrentTags
            'test35-14'
        );

        $asit->replacePkey( $orgPkey, $orgPkey ); // replace by itself - test ??
        $orgIndex2 = $asit->getPkeyIndexes( [ $orgPkey ] );
        $this->assertEquals(
            $orgIndex, $orgIndex2[0],
            'test35-15 org : ' . $orgIndex . ', org2 : ' . $orgIndex2[0]
        );

        $ok = 0;
        try {
            $asit->replacePkey( 'notFound', $otherPkey ); // found setPkey + exception (org not found)
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test35-16, exp 2, got ' . $ok );

        $ok = 0;
        try {
            $asit->replacePkey( $orgPkey, $otherPkey ); // found orgPkey + exception (but otherPkey exists)
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test35-17, exp 2, got ' . $ok );

        $newPkey = 'key9876';
        $tags    = $asit->getTags( $orgPkey );
        $asit->replacePkey( $orgPkey, $newPkey ); // alter pKey, replacePkey, ok
        $this->assertFalse(
            $asit->pKeyExists( $orgPkey ),
            'test35-18'
        );
        $this->assertEquals(
            $asit->key(), $asit->getPkeyIndexes( [ $newPkey ] )[0],
            'test35-19'
        );

        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            'test35-20 current ix :' . $asit->key()
        );
        $this->assertEquals(
            [ $orgIndex => $current ], $asit->get( $newPkey ),
            'test35-21'
        );
        $this->assertEquals(
            $tags, $asit->getTags( $newPkey ),
            'test35-22'
        );

        $orgPkey = $asit->getCurrentPkey();
        $newPkey = 'testbcdefg';
        $asit->setCurrentPkey( $newPkey ); // alter pKey, replacePkey, ok
        $this->assertTrue(
            $orgPkey != $asit->getCurrentPkey(),
            'test35-23'
        );
        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            'test35-24'
        );

        $ok = 0;
        try {
            $asit->last()->next()->next()->next()->setCurrentPkey( $newPkey ); // setCurrentPkey + out of pos
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test35-25, exp 2, got ' . $ok );

        $duplPkey = $asit->last()->previous()->previous()->previous()->getCurrentPkey();
        $asit->previous()->previous()->previous()->previous()->previous()->previous(); // set another current
        $ok = 0;
        try {
            $asit->setCurrentPkey( $duplPkey ); // setCurrentPkey + dupl pKey (i.e. key exists)
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test35-26, exp 2, got ' . $ok );
    }

    /**
     * Testing Assittag addPkeyTag + exception (not found pKey)
     *
     * @test
     */
    public function asitTest42() {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->addPkeyTag( 'fakePkey', 'testNewTag' ); // not found addPkeyTag + exception (not found pKey)
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test42-1, exp 2, got ' . $ok );

    }

    /**
     * Testing Asittag getCurrentTags + exception (no current)
     *
     * @test
     */
    public function asitTest43() {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->getCurrentTags();  // getCurrentTags + exception (no current)
            $ok = 1;
        }
        catch( RunTimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test43-1, exp 2, got ' . $ok );

    }

    /**
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest44() {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->addCurrentTag( 'testnyTag' ); // addCurrentTag + exception (no current)
            $ok = 1;
        }
        catch( RuntimeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test44-1, exp 2, got ' . $ok );


    }

    /**
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest45() {
        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $asit->seek( array_rand( array_flip( range( 450, 454 ))));

        $ok = 0;
        try {
            $asit->addCurrentTag( [] );  // addCurrentTag + exception (invalid tag)
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test45-6, exp 2, got ' . $ok );

        $newTag = 'newTag';
        $this->assertFalse(
            $asit->hasPkeyTag( 'fakePkey', $newTag ), // not found pKey
            'test407'
        );

        $pKey   = $asit->getCurrentPkey();
        $tags   = $asit->getCurrentTags();
        // position somewhere else
        $asit->seek( array_rand( array_flip( range( 455, 459 ))));

        $asit->addPkeyTag( $pKey, $newTag );
        $tags[] = $newTag;
        foreach( $tags as $testTag ) {
            $this->assertTrue(
                $asit->hasPkeyTag( $pKey, $testTag ),
                'test45-8'
            );
        }
        $this->assertfalse(
            $asit->hasPkeyTag( $pKey, 'fakeTag' ),
            'test45-9'
        );

    }

    /**
     * Testing Asittag - primary key - getPkeys method
     *
     * @test
     */
    public function asitTest51() {

        $asit = new Asittag();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                (( 0 == ( $pIx % 2 )) ? 'KEY' : 'key' ) . $pIx
            );
        } // end for

        $this->assertTrue(
            $asit->count() == count( $asit->getPkeys()),
            'test51-1'
        );
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest52() {

        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $asit->seek( array_rand( array_flip( range( 520, 529 ))));

        $currentValue = $asit->current();
        $currentags   = $asit->getCurrentTags();   // test getCurrentTags
        $currentPkey  = $asit->getCurrentPkey();
        $pKeytags1    = $asit->getTags( $currentPkey );   // test getTags( $pKey )
        $this->assertEquals(
            $currentags, $pKeytags1,
            'test52-1 - currenTags : ' . implode( ',', $currentags ) . ' <-> pKeytags1 : ' . implode( ',', $pKeytags1 )
        );
        $this->assertTrue(
            [] == $asit->getTags( 'fake pKey' ),
            'test52-2'
        );
        $pKeytags2   = $asit->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertEquals(
            $pKeytags1, $pKeytags2,
            'test52-3 - tags1 : ' . implode( ',', $pKeytags1 ) . ' <-> tags2 : ' . implode( ',', $pKeytags2 )
        );

        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $pKeytags1[0] )), // getPkeys( tag )
            'test52-5'
        );
        $this->assertTrue(
            [] == $asit->getPkeys( 'fakeTag' ),
            'test52-6'
        );

        $allTags = $asit->getTags();
        foreach( self::$COLORS as $attribute ) {
            $this->assertTrue(
                in_array( $attribute, $allTags ),
                'test52-7'
            );
            $attrCnt = $asit->tagCount( $attribute ); // test tagCount( <tag> )
            $this->assertTrue(
                100 == $attrCnt,
                'test52-8 - tag : ' . $attribute . ', count: ' . $attrCnt
            );
        }
        $elements1 = $asit->get( null, $currentags ); // test get( null, $tags1 ) : all with currentags
        $this->assertTrue(
            in_array( $currentValue, $elements1 ),
            'test52-9 exp : ' . $currentValue . ', got ' . var_export( $elements1, true )
        );
        $this->assertTrue(
            100 == count( $elements1 ),
            'test52-10 exp : 100, got ' . count( $elements1 )
        );

    }

    /**
     * Testing tag - getTags method
     *
     * @test
     */
    public function asitTest55() {

        $asit = new Asittag();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                $pIx,
                [ (( 0 == ( $pIx % 2 )) ? 'TAG' : 'tag' ) . $pIx ] // unique tag
            );
        } // end for

        foreach( $asit->getTags( null, SORT_FLAG_CASE | SORT_STRING ) as $tIx => $tag ) {
            // case-insensitively sort
            $exp = ( 0 == ( $tIx % 2 )) ? 'TAG' :  'tag';
//          echo ' 1 got : ' . $tag . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $tag, 0, 3 ),
                'test55-2-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }
        foreach( $asit->getTags() as $tIx => $tag ) {
            // case-sensitively sort
            $exp = ( $tIx < 5 ) ? 'TAG' : 'tag';
//          echo ' 2 got : ' . $tag . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $tag, 0, 3 ),
                'test55-3-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest61() {
        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $asit->seek( array_rand( array_flip( range( 610, 619 ))));

        $currentPkey = $asit->getCurrentPkey();
        $lightGray   = 'lightGray';
        $deepBlue    = 'deepBlue';
        $asit->addPkeyTag( $currentPkey, $lightGray );  //addPkeyTag
        $asit->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag
        $asit->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag, duplicates eliminated
        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $lightGray )), // getPkeys( tag )
            'test61-1 exp : ' . $currentPkey . ', got ' . implode( ',', $asit->getPkeys( $lightGray )) .
            ', search on tag ' . $lightGray . ', current has ' . implode( ',', $asit->getCurrentTags())
        );
        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $deepBlue )), // getPkeys( tag )
            'test61-2 exp : ' . $currentPkey . ', got ' . implode( ',', $asit->getPkeys( $deepBlue )) .
            ', search on tag ' . $deepBlue . ', current has ' . implode( ',', $asit->getCurrentTags())
        );

        $lightBlue  = 'lightBlue';
        $asit->addCurrentTag( $lightBlue );

        $numericTag = 1234;
        $asit->addCurrentTag( $numericTag );

        $pKeytags3   = $asit->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertTrue(
            5 == count( $pKeytags3 ),
            'test61-3'
        );
        $this->assertTrue(
            in_array( $lightGray, $pKeytags3 ),
            'test61-4'
        );
        $this->assertTrue(
            in_array( $deepBlue, $pKeytags3 ),
            'test61-5'
        );
        $this->assertTrue(
            in_array( $lightBlue, $pKeytags3 ),
            'test61-6'
        );
        $this->assertTrue(
            in_array( $numericTag, $pKeytags3 ),
            'test61-7'
        );

        $current   = $asit->current(); // current value
        $currentIx = $asit->key();
        $this->assertEquals(
            [ $currentIx => $current ], $asit->tagGet( $lightBlue ),
            'test61-8'
        );
        $this->assertEquals(
            [ $currentIx => $current ], $asit->tagGet( $pKeytags3, true ),
            'test61-9'
        );

        $newElement = 'element1234';
        $newPkey    = 1234;
        $newTags    = [
            $lightGray,
            $deepBlue,
            $lightBlue
        ];
        $asit->append( $newElement, $newPkey, $newTags ); // append( value, pkey, tags )
        $newIx = $asit->key();
        $this->assertEquals(
            [ $currentIx => $current, $newIx => $newElement ],
            $asit->tagGet( $newTags, true ), // tagGet
            'test61-20'
        );

        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            'test61-21'
        );

        foreach( $newTags as $tag ) {
            $this->assertTrue(
                $asit->hasCurrentTag( $tag ),  // hasCurrentTag
                'test61-22 current has : ' . implode( ',', $asit->getCurrentTags()) . ' exp : ' . $tag
            );
        }
        $this->assertFalse(
            $asit->hasCurrentTag( 'fake' ),
            'test61-23'
        );

        $newTags[] = 'fake'; // no one has 'fake', will be skipped in get/tagGet
        $this->assertEquals(
            2, count( $asit->tagGet( $newTags, true )), // tagGet
            'test61-24'
        );
        $this->assertEquals(
            [ $currentIx => $current, $newIx => $newElement ],
            $asit->tagGet( $newTags, true ), // tagGet
            'test61-25'
        );

    }

    /**
     * Testing Asittag removePkeyTag
     *
     * @test
     */
    public function asitTest62() {

        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for

        $ok   = 0;
        try {
            $asit->removePkeyTag( 'fakePkey', 'fakeTag' );
            $ok = 1;
        } catch( RuntimeException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 1, 'test62-1, exp 1, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( 'fakePkey', $asit->getCurrentTags()[0] );
            $ok = 1;
        } catch( InvalidArgumentException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test62-2, exp 2, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( $asit->getCurrentPkey(), 'fakeTag' );
            $ok = 1;
        } catch( RuntimeException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 1, 'test62-3, exp 1, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 620, 624 ))));

        $pKey = $asit->getCurrentPkey();
        $tags = $asit->getPkeyTags( $pKey );
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test62-4'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // but tag exists
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test62-5'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test62-6'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // tag don't exists, no exception
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test62-7'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test62-8'
        );

        $asit->last()->next()->next()->next()->next();
        $ok   = 0;
        try {
            $asit->removeCurrentTag( 'fakeTag' );
            $ok = 1;
        } catch( RuntimeException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test62-9, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 625, 629 ))));

        $tags = $asit->getCurrentTags();
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasCurrentTag( $tags[0] ),
            'test62-10, exp : ' . $tags[0]
        );
        $asit->removeCurrentTag( $tags[0] );
        $this->assertFalse(
            $asit->hasCurrentTag( $tags[0] ),
            'test62-11'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test62-12'
        );
        $this->assertTrue(
            $asit->tagExists( $tags[0] ),
            'test62-13'
        );


        foreach( array_keys( $asit->tagGet( $tags[0] )) as $tIx ) {
            $asit->seek( $tIx );
            $asit->removeCurrentTag( $tags[0] );
        }
        $this->assertEquals(
            0,
            $asit->tagCount( $tags[0] ),
            'test62-14'
        );
        $this->assertFalse(
            $asit->tagExists( $tags[0] ),
            'test62-15'
        );
    }

    /**
     * Testing Asittag get - no found pKey
     *
     * @test
     */
    public function asitTest63() {
        $data = $this->arrayLoader();
        $asit = new Asittag();
        foreach( $data as $element ) {
            $asit->append( $element );
        }
        $this->assertEquals(
            [],
            $asit->get( 'fakePkey' ),
            'test63-1'
        );
        $this->assertEquals(
            array_values( $data ),
            $asit->get(),
            'test63-2'
        );
    }

    /**
     * Testing Asittag selecting on tags
     *
     * @test
     */
    public function asitTest65() {
        $asit = new Asittag();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $asit->seek( array_rand( array_flip( range( 650, 659 ))));

        $BIG       = 'big';
        $FAST      = 'fast';
        $ECOLOGIC  = 'ecologic';
        $CHEAP     = 'sheap';
        $ABRAND    = 'brand A';
        $LOWQUAL   = 'low quality';
        foreach(   $asit   as $key => $value ) { // 'internal key', NOT pKey
            if( 0 == $key % 100 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $FAST );
            }
            if( 0 == $key % 30 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $BIG );
            }
            if( 0 == $key % 15 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ABRAND );
            }
            if( 0 == $key % 11 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $LOWQUAL );
            }
            if( 0 == $key % 5 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ECOLOGIC );
            }
            if( 0 == $key % 4 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $CHEAP );
            }
        } // end foreach
        $selection = $asit->tagGet( $FAST );
        $this->assertTrue(
            10 == count( $selection ),
            'test651 exp 10 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG );
        $this->assertTrue(
            34 == count( $selection ),
            'test652 exp 34 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG, null, $FAST );
        $this->assertTrue(
            30 == count( $selection ),
            'test653 exp 30 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [$FAST, $ECOLOGIC], true );
        $this->assertTrue(
            10 == count( $selection ),
            'test654 exp 10 : got : ' . count( $selection )
        );

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], true, $BIG );
        $this->assertTrue(
            6 == count( $selection ),
            'test655 exp 6 : got : ' . count( $selection )
        );

        /*
        foreach( array_keys( $selection ) as $kIx ) {
            $asit->seek( $kIx );
            echo $kIx . ' '  . implode( ',', $asit->getCurrentTags()) . PHP_EOL; // test ###
        }
        */

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false );
        $this->assertTrue(
            200 == count( $selection ),
            'test654 exp 200 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false, [ $BIG, $CHEAP ] );
        $this->assertTrue(
            133 == count( $selection ),
            'test656 exp 133 : got : ' . count( $selection )
        );
    }

    /**
     * Test Asit / Asittag append Pkey + InvalidArgumentException, duplicate + invalid pKey
     *
     * @test
     */
    public function asitTest81() {
        $data = [ 'key' => 'value' ];
        foreach( [ Asit::factory( $data ), Asittag::factory( $data ) ] as $aIx => $asit ) {
            $ok   = 0;
            try {
                $asit->append( 'value', 'key' );  // duplicate pKey
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test81-1-' . $aIx . ', exp 2, got ' . $ok );

            $ok = 0;
            try {
                $asit->append( 'value2', [ 1, 2, 3 ] ); // unvalid key
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test81-2-' . $aIx . ', exp 2, got ' . $ok );

        } // end foreach

    }

    /**
     * Test Asittag append Pkey + InvalidArgumentException, invaid tag
     *
     * @test
     */
    public function asitTest82() {
        $asit = Asittag::factory( [ 'key' => 'value' ] );

        $pKey = 'key2';
        $xTag = [ 'tag1', 'tag2', [], 1.2345 ];
        $ok = 0;
        try {
            $asit->append( 'element2', $pKey, $xTag ); // invalid tag array + float
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( RuntimeException $e ) {
            $ok = 3;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test82-3, exp 2, got ' . $ok );
    }

    /**
     * Test Asit getCurrentPkey + RuntimeException
     *
     * @test
     */
    public function asitTest9() {
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
        $this->assertTrue( $ok == 2, 'test9-1, exp 2, got ' . $ok );
    }

    /**
     * Test get, no args
     *
     * @test
     */
    public function asitTest10() {
        $asit = new Asit();
        foreach( $this->arrayLoader() as $key => $value ) {
            $asit->append( $value, $key );
//          $asit->addPkeyTag( $key, $this->getAttribute( $asit->key()));
        } // end for
        $this->assertEquals(
            count( $asit->getPkeys()),
            count( $asit->get()),
            'test 10-1'
        );
    }

}