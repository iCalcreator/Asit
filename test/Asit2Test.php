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
use Kigkonsult\Asit\Exceptions\TagException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class Asit2Test extends AsitBaseTest
{
    /**
     * dataProvider
     *
     * @return array
     */
    public static function pKeyTagProvider() : array
    {
        $testData = [];

        $testData[] = [ __FUNCTION__ . 1, 'tag1', true ];
        $testData[] = [ __FUNCTION__ . 2, '0', true ];
        $testData[] = [ __FUNCTION__ . 3, 0, true ];
        $testData[] = [ __FUNCTION__ . 4, true, false ];
        $testData[] = [ __FUNCTION__ . 5, 5.5, false ];

        return $testData;
    }

    /**
     * Test Asit assertPkey
     *
     * @test
     * @dataProvider pKeyTagProvider
     *
     * @param string $case
     * @param mixed $pKey
     * @param bool $exp
     */
    public function asitTest21Pkey( string $case, mixed $pKey, bool $exp ) : void
    {
        $ok = false;
        try {
            Asit::assertPkey( $pKey );
            $ok = true;
        }
        catch( PkeyException $e ) {
            $ok = false;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( $exp, $ok, __FUNCTION__ . ' #' . $case . ' exp 2, got ' . $ok );
    }

    /**
     * Test Asittag assertTag
     *
     * @test
     * @dataProvider pKeyTagProvider
     *
     * @param string $case
     * @param mixed  $tag
     * @param bool   $exp
     */
    public function asitTest24Tag( string $case, mixed $tag, bool $exp ) : void
    {
        $ok = false;
        try {
            Asittag::assertTag( $tag );
            $ok = true;
        }
        catch( TagException ) {
            $ok = false;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( $exp, $ok, __FUNCTION__ . ' #' . $case . ' exp 2, got ' . $ok );
    }

    /**
     * Testing Asittag 'current' methods :
     *    getCurrentTags, hasCurrentTag, addCurrentTag,
     *
     * @test
     */
    public function asitTest32() : void
    {

        $asit = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end foreach
        $asit->rewind()->previous();

        // no valid current
        $ok = 0;
        try {
            $asit->getCurrentTags();
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #1, exp 2, got ' . $ok );

        // no current
        $ok = 0;
        try {
            $asit->hasCurrentTag( 'fake ');
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #2, exp 2, got ' . $ok );

        // no current + invalid tag
        $ok = 0;
        try {
            $asit->addCurrentTag( '' );
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {

            echo __METHOD__ . ' got : ' . $e::class . PHP_EOL; // test ###

            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #3, exp 2, got ' . $ok );

        // no current
        $ok = 0;
        try {
            $asit->addCurrentTag( 'fake ' );
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #4, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 30, 39 )))); // set valid current

        $tags1 = $asit->getCurrentTags();
        $this->assertCount(
            1, $tags1, __FUNCTION__ . ' #5 exp 1, got ' . implode( ',', $tags1 )
        );

        $newTag = 'newTag';
        $this->assertFalse(
            $asit->hasCurrentTag( $newTag ),
            __FUNCTION__ . ' #5'
        );
        $asit->addCurrentTag( $newTag );
        $this->assertTrue(
            $asit->hasCurrentTag( $newTag ),
            __FUNCTION__ . ' #6'
        );
        $this->assertEquals(
            1, $asit->tagCount( $newTag ), __FUNCTION__ . ' #7'
        );

        $tags2 = $asit->getCurrentTags();
        $this->assertCount(
            2, $tags2, __FUNCTION__ . ' #8 exp 2, got ' . implode( ',', $tags2 )
        );

        $this->assertFalse(
            $asit->hasCurrentTag( '' ),
            __FUNCTION__ . ' #9'
        );

        $this->assertFalse(
            $asit->hasCurrentTag( 0 ),
            __FUNCTION__ . ' #10'
        );

        $asit = null;
    }

    /**
     * Testing Asittag other primary key + tag methods
     *
     * Asittag::pKeyTagGet() tests with no pKeys but tag and union IN asitTest38
     *
     * @test
     */
    public function asitTest37() : void
    {
        $asit = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $asit->seek( array_rand( array_flip( range( 30, 34 ))));
        $pKey1 = $asit->getCurrentPkey();
        $ix1   = $asit->key();

        $asit->seek( array_rand( array_flip( range( 35, 39 ))));
        $pKey2 = $asit->GetCurrentPkey();
        $ix2   = $asit->key();

        $search = [ $pKey1, $pKey2 ];

        // pKeyTagGet + pKey/tag tests
        $tags1 = $asit->getTags( $pKey1 );
        $this->assertEquals(
            [ $ix1 => 'element' . $ix1 ],
            $asit->pKeyTagGet( $search, $tags1 ),  // get( pKeys, tags )
            __FUNCTION__ . ' #10 search : ' .
            implode( ',', $search ) . ' tags : ' . implode( ',', $tags1 ) .
            ' got : ' . implode( ',', $asit->pKeyTagGet( $search, $tags1 ))
        );

        $this->assertEquals(
            [ $ix2 => 'element' . $ix2 ], $asit->pKeyTagGet( $search, $asit->getTags( $pKey2 ) ),  // get( pKeys, tags )
            __FUNCTION__ . ' #11'
        );
        $tags = array_merge(
            $asit->getTags( $pKey1 ),
            $asit->getTags( $pKey2 )
        );
        $this->assertEquals(
            [], $asit->pKeyTagGet( $search, $tags, true ),  // get( pKeys, tags )
            __FUNCTION__ . ' #12'
        );
        $this->assertEquals(
            [  $ix1 => 'element' . $ix1, $ix2 => 'element' . $ix2 ],
            $asit->pKeyTagGet( $search, $tags, false ),  // get( pKeys, tags )
            __FUNCTION__ . ' #13'
        );

        $asit->seek( array_rand( array_flip( range( 50, 84 ))));
        $otherPkey = $asit->getCurrentPkey();

        $asit->seek( array_rand( array_flip( range( 85, 99 ))));
        $orgPkey   = $asit->getCurrentPkey();
        $orgIndex  = $asit->key();
        // echo  'current ix :' . $asit->key() . ', pKey : ' . $orgPkey; // test ###

        $current   = $asit->current();
        $newTag    = 'someOtherColor';
        $asit->addCurrentTag( $newTag );
        $this->assertContains(
            $newTag, $asit->getCurrentTags(), __FUNCTION__ . ' #14'
        );

        $newPkey = 'key9876';
        $tags    = $asit->getTags( $orgPkey );
        $asit->replacePkey( $orgPkey, $newPkey ); // alter pKey, replacePkey, ok
        $this->assertFalse(
            $asit->pKeyExists( $orgPkey ),
            __FUNCTION__ . ' #18'
        );

        $orgIndex2 = $asit->getPkeyIndexes( [ $newPkey ] );
        $orgIndex1 = reset( $orgIndex2 );
        $this->assertEquals(
            $asit->key(), $orgIndex1,
            __FUNCTION__ . ' #19 exp : ' . $asit->key() . ', got : ' . implode( ',', $orgIndex2 )
        );

        $this->assertEquals(
            $newPkey,
            $asit->getCurrentPkey(),
            __FUNCTION__ . ' #20 current ix :' . $asit->key()
        );
        $this->assertEquals(
            [ $orgIndex => $current ],
            $asit->pKeyTagGet( $newPkey ),
            __FUNCTION__ . ' #21'
        );
        $this->assertEquals(
            $tags, $asit->getTags( $newPkey ),
            __FUNCTION__ . ' #22'
        );

        $orgPkey = $asit->getCurrentPkey();
        $newPkey = 'testbcdefg';
        $asit->setCurrentPkey( $newPkey ); // alter pKey, replacePkey, ok
        $this->assertNotEquals(
            $orgPkey, $asit->getCurrentPkey(), __FUNCTION__ . ' #23'
        );
        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            __FUNCTION__ . ' #24'
        );

        $asit = null;
    }

    /**
     * Testing Asittag::pKeyTagGet() with no pKeys but tag and union
     *
     * @test
     */
    public function asitTest38() : void
    {
        static $VALUE = 'value';
        static $PKEY  = 'pKey';
        static $TAG   = 'tag';
        $asit = Asittag::factory()
            ->append( $VALUE . 1, $PKEY . 1, $TAG )
            ->append( $VALUE . 2, $PKEY . 2, $TAG . 2 )
            ->append( $VALUE . 3, $PKEY . 3, $TAG )
            ->append( $VALUE . 4, $PKEY . 4, $TAG . 4 )
            ->append( $VALUE . 5, $PKEY . 5, [ $TAG, $TAG . 5 ] );

        $this->assertEquals(  // not found
            [],
            $asit->PkeyTagGet( null, $TAG . 6 ),
            __METHOD__ . ' #1'
        );

        $this->assertEquals( // incompatible tags, union true
            [],
            $asit->PkeyTagGet( null, [ $TAG . 2, $TAG . 4 ], true ),
            __METHOD__ . ' #2'
        );

        $this->assertEquals( // all with any of the tags, union false
            [ 1 => $VALUE . 2, 3 => $VALUE . 4 ],
            $asit->PkeyTagGet( null, [ $TAG . 2, $TAG . 4 ], false ),
            __METHOD__ . ' #3'
        );

        $this->assertEquals( // all with tag
            [ 0 => $VALUE . 1, 2 => $VALUE . 3, 4 => $VALUE . 5 ],
            $asit->PkeyTagGet( null, $TAG ),
            __METHOD__ . ' #4'
        );

        $this->assertEquals(  // all with both tags, union true
            [ 4 => $VALUE . 5 ],
            $asit->PkeyTagGet( null, [ $TAG, $TAG . 5 ], true ),
            __METHOD__ . ' #5'
        );

        $this->assertEquals(  // all with any of the tags, union false
            [ 0 => $VALUE . 1, 2 => $VALUE . 3, 3 => $VALUE . 4, 4 => $VALUE . 5 ],
            $asit->PkeyTagGet( null, [ $TAG, $TAG . 4 ], false ),
            __METHOD__ . ' #6'
        );
    }

    /**
     * Testing Asit/Asmit replacePkey and reuse key
     *
     * @test
     */
    public function asitTest41() : void
    {
        foreach(
            [ new Asit( self::arrayLoader( 10 )), new Asmit( self::arrayLoader( 10 )) ]
            as $tx => $asit ) {

            $asit->seek( array_rand( array_flip( range( 0, 8 ))));
            $pKey1   = $asit->getCurrentPkey();
            $ix1     = $asit->key();

            $asit->replacePkey( $pKey1, $pKey1 ); // replace by itself - test ??
            $orgIndex2 = array_values( $asit->getPkeyIndexes( [ $pKey1 ] ));
            $this->assertEquals(
                [ $ix1 ],
                $orgIndex2,
                __FUNCTION__ . ' #1-' . $tx . ' org : ' . $ix1 . ', org2 : ' . var_export( $orgIndex2, true )
            );

            $newPkey = 'testbcdefg';

            $asit->replacePkey( $pKey1, $newPkey );
            $this->assertFalse(
                $asit->pKeyExists( $pKey1 ),
                __FUNCTION__ . ' #2-' . $tx
            );
            $this->assertEquals(
                $ix1, $asit->key(),
                __FUNCTION__ . ' #3-' . $tx
            );
            $this->assertEquals(
                $newPkey,
                $asit->getCurrentPkey(),
                __FUNCTION__ . ' #4-' . $tx
            );

            $asit->next();
            $pKey2 = $asit->getCurrentPkey();
            $ix2   = $asit->key();
            $asit->replacePkey( $pKey2, $pKey1 );
            $this->assertFalse(
                $asit->pKeyExists( $pKey2 ),
                __FUNCTION__ . ' #5-' . $tx
            );
            $this->assertEquals(
                $ix2, $asit->key(),
                __FUNCTION__ . ' #6-' . $tx
            );
            $this->assertEquals(
                $pKey1,
                $asit->getCurrentPkey(),
                __FUNCTION__ . ' #7-' . $tx
            );

            $asit = null;
        }
    }

    /**
     * Testing Assittag addPkeyTag + exception (not found pKey)
     *
     * @test
     */
    public function asitTest52() : void
    {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->addPkeyTag( 'fakePkey', 'testNewTag' ); // not found addPkeyTag + exception (not found pKey)
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
     * Testing Asittag getCurrentTags + exception (no current)
     *
     * @test
     */
    public function asitTest53() : void
    {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->getCurrentTags();  // getCurrentTags + exception (no current)
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
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest54() : void
    {
        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->addCurrentTag( 'testnyTag' ); // addCurrentTag + exception (no current)
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
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest55() : void
    {
        $asittag = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asittag->append( $value, $key );
            $asittag->addPkeyTag( $key, self::getAttribute( $asittag->key() ) );
        } // end foreach
        $asittag->seek( array_rand( array_flip( range( 40, 44 ))));

        $newTag = 'newTag';
        $this->assertFalse(
            $asittag->hasPkeyTag( 'fakePkey', $newTag ), // not found pKey
            __FUNCTION__ . ' #1'
        );

        $pKey   = $asittag->getCurrentPkey();
        $tags   = $asittag->getCurrentTags();
        // position somewhere else
        $asittag->seek( array_rand( array_flip( range( 45, 49 ))));

        $asittag->addPkeyTag( $pKey, $newTag );
        $tags[] = $newTag;
        foreach( $tags as $testTag ) {
            $this->assertTrue(
                $asittag->hasPkeyTag( $pKey, $testTag ),
                __FUNCTION__ . ' #3'
            );
        }
        $this->assertfalse(
            $asittag->hasPkeyTag( $pKey, 'fakeTag' ),
            __FUNCTION__ . ' #4'
        );

        $asittag = null;
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest62() : void
    {

        $asitTag = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asitTag->append( $value, $key );
            $asitTag->addPkeyTag( $key, self::getAttribute( $asitTag->key() ) );
        } // end for
        $asitTag->seek( array_rand( array_flip( range( 50, 59 ))));

        $currentValue = $asitTag->current();
        $currentags   = $asitTag->getCurrentTags();   // test getCurrentTags
        $currentPkey  = $asitTag->getCurrentPkey();
        $pKeytags1    = $asitTag->getTags( $currentPkey );   // test getTags( $pKey )
        $this->assertEquals(
            $currentags, $pKeytags1,
            __FUNCTION__ . ' #1 - currenTags : ' . implode( ',', $currentags ) .
                ' <-> pKeytags1 : ' . implode( ',', $pKeytags1 )
        );
        $this->assertEquals(
            [], $asitTag->getTags( 'fake pKey' ), 'test62-2'
        );
        $pKeytags2   = $asitTag->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertEquals(
            $pKeytags1,
            $pKeytags2,
            __FUNCTION__ . ' #3 - tags1 : ' . implode( ',', $pKeytags1 ) .
                ' <-> tags2 : ' . implode( ',', $pKeytags2 )
        );

        $this->assertContains(
//          $currentPkey, $asitTag->getPkeys( $pKeytags1[0], null ), 'test62-5'
            $currentPkey, $asitTag->getPkeys( $pKeytags1[0] ), __FUNCTION__ . ' #5'
        );
        $this->assertEquals(
//          [], $asitTag->getPkeys( 'fakeTag', null ), 'test62-6'
            [], $asitTag->getPkeys( 'fakeTag' ), __FUNCTION__ . ' #6'
        );

        $allTags = $asitTag->getTags();
        foreach( self::$COLORS as $attribute ) {
            $this->assertContains(
                $attribute, $allTags, __FUNCTION__ . ' #7'
            );
            $attrCnt = $asitTag->tagCount( $attribute ); // test tagCount( <tag> )
            $this->assertEquals(
                10, $attrCnt, __FUNCTION__ . ' #8 - tag : ' . $attribute . ', count: ' . $attrCnt
            );
        }
        $elements1 = $asitTag->pKeyTagGet( null, $currentags ); // test get( null, $tags1 ) : all with currentags
        $this->assertContains(
            $currentValue, $elements1, __FUNCTION__ . ' #9 exp : ' . $currentValue . ', got ' . var_export( $elements1, true )
        );
        $this->assertCount(
            10, $elements1, __FUNCTION__ . ' #10 exp : 100, got ' . count( $elements1 )
        );

        $asitTag = null;
    }

    /**
     * Testing tag - getTags method
     *
     * @test
     */
    public function asitTest65() : void
    {
        $asit = new Asittag();
        for( $pIx = 0; $pIx < 10; $pIx++ ) {
            $asit->append(
                'element',
                $pIx,
                [ (( 0 === ( $pIx % 2 )) ? 'TAG' : 'tag' ) . $pIx ] // unique tag
            );
        } // end for

        $tags = $asit->getTags( null, SORT_FLAG_CASE | SORT_STRING );
        $tIx = -1;
        foreach( $tags as $tag ) {
            ++$tIx;
            // case-insensitively sort
            $exp = ( 0 === ( $tIx % 2 )) ? 'TAG' :  'tag';
//          echo ' 1 got : ' . $tag . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $tag, 0, 3 ),
                __FUNCTION__ . ' #2-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }
        foreach( $asit->getTags() as $tIx => $tag ) {
            // case-sensitively sort
            $exp = ( $tIx < 5 ) ? 'TAG' : 'tag';
//          echo ' 2 got : ' . $tag . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $tag, 0, 3 ),
                __FUNCTION__ . ' #3-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }

        $asit = new Asittag();

        $ok = 0;
        try {
            $asit->getTags( true ); // false|int|string exp
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #4, exp 2, got ' . $ok );

        $ok = 0;
        try {
            Asittag::factory()->assertTag( '' ); // false|int|string exp
            $ok = 1;
        }
        catch( TagException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #5, exp 2, got ' . $ok );
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest71() : void
    {
        $asitTag = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asitTag->append( $value, $key );
            $asitTag->addPkeyTag( $key, self::getAttribute( $asitTag->key() ) );
        } // end for
        $asitTag->seek( array_rand( array_flip( range( 61, 69 ))));

        $currentPkey = $asitTag->getCurrentPkey();
        $lightGray   = 'lightGray';
        $deepBlue    = 'deepBlue';
        $asitTag->addPkeyTag( $currentPkey, $lightGray );  //addPkeyTag
        $asitTag->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag
        $asitTag->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag, duplicates eliminated
        $hayStack    = $asitTag->getPkeys( $lightGray );
        $this->assertContains(
            $currentPkey,
            $hayStack,
            __FUNCTION__ . ' #1 exp : ' . $currentPkey . ', got ' . implode( ',', $hayStack ) .
            ', search on tag ' . $lightGray . ', current has ' . implode( ',', $asitTag->getCurrentTags() )
        );
        $hayStack    = $asitTag->getPkeys( $deepBlue );
        $this->assertContains(
            $currentPkey,
            $hayStack,
            __FUNCTION__ . ' #2 exp : ' . $currentPkey . ', got ' . implode( ',', $hayStack ) .
            ', search on tag ' . $deepBlue . ', current has ' . implode( ',', $asitTag->getCurrentTags() )
        );

        $lightBlue  = 'lightBlue';
        $asitTag->addCurrentTag( $lightBlue );

        $numericTag = 1234;
        $asitTag->addCurrentTag( $numericTag );

        $pKeytags3   = $asitTag->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertCount(
            5, $pKeytags3, __FUNCTION__ . ' #3'
        );
        $this->assertContains(
            $lightGray, $pKeytags3, __FUNCTION__ . ' #4'
        );
        $this->assertContains(
            $deepBlue, $pKeytags3, __FUNCTION__ . ' #5'
        );
        $this->assertContains(
            $lightBlue, $pKeytags3, __FUNCTION__ . ' #6'
        );
        $this->assertContains(
            $numericTag, $pKeytags3, __FUNCTION__ . ' #7'
        );

        $current   = $asitTag->current(); // current value
        $currentIx = $asitTag->key();
        $this->assertEquals(
            [ $currentIx => $current ], $asitTag->tagGet( $lightBlue ),
            __FUNCTION__ . ' #8'
        );
        $this->assertEquals(
            [ $currentIx => $current ], $asitTag->tagGet( $pKeytags3, true ),
            __FUNCTION__ . ' #9'
        );

        $newElement = 'element1234';
        $newPkey    = 1234;
        $newTags    = [
            $lightGray,
            $deepBlue,
            $lightBlue
        ];
        $asitTag->append( $newElement, $newPkey, $newTags ); // append( value, pkey, tags )
        $newIx = $asitTag->key();
        $this->assertEquals(
            [ $currentIx => $current, $newIx => $newElement ],
            $asitTag->tagGet( $newTags, true ), // tagGet
            __FUNCTION__ . ' #20'
        );

        $this->assertEquals(
            $newPkey, $asitTag->getCurrentPkey(),
            'test71-21'
        );

        foreach( $newTags as $tag ) {
            $this->assertTrue(
                $asitTag->hasCurrentTag( $tag ),  // hasCurrentTag
                'test71-22 current has : ' . implode( ',', $asitTag->getCurrentTags()) . ' exp : ' . $tag
            );
        }
        $this->assertFalse(
            $asitTag->hasCurrentTag( 'fake' ),
            'test71-23'
        );

        $newTags[] = 'fake'; // no one has 'fake', will be skipped in get/tagGet
        $this->assertCount(
            2, $asitTag->tagGet( $newTags, true ), // tagGet
            'test71-24'
        );
        $this->assertEquals(
            [ $currentIx => $current, $newIx => $newElement ],
            $asitTag->tagGet( $newTags, true ), // tagGet
            'test71-25'
        );

        $asitTag = null;
    }

    /**
     * Testing Asittag removePkeyTag
     *
     * @test
     */
    public function asitTest72() : void
    {
        $asit = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for

        $ok   = 0;
        try {
            $asit->removePkeyTag( 'fakePkey', 'fakeTag' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 1, $ok, __FUNCTION__ . ' #1, exp 1, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( 'fakePkey', $asit->getCurrentTags()[0] );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #2, exp 2, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( $asit->getCurrentPkey(), 'fakeTag' );
            $ok = 1;
        }
        catch( PkeyException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 1, $ok, __FUNCTION__ . ' #3, exp 1, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 62, 66 ))));

        $pKey = $asit->getCurrentPkey();
        $tags = $asit->getPkeyTags( $pKey );
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            __FUNCTION__ . ' #4'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // but tag exists
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            __FUNCTION__ . ' #5'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            __FUNCTION__ . ' #6'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // tag don't exists, no exception
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            __FUNCTION__ . ' #7'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            __FUNCTION__ . ' #8'
        );

        $asit->last()->next()->next()->next()->next();
        $ok   = 0;
        try {
            $asit->removeCurrentTag( 'fakeTag' );
            $ok = 1;
        }
        catch( PositionException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals( 2, $ok, __FUNCTION__ . ' #9, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 67, 69 ))));

        $tags = $asit->getCurrentTags();
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasCurrentTag( $tags[0] ),
            __FUNCTION__ . ' #10, exp : ' . $tags[0]
        );
        $asit->removeCurrentTag( $tags[0] );
        $this->assertFalse(
            $asit->hasCurrentTag( $tags[0] ),
            __FUNCTION__ . ' #11'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            __FUNCTION__ . ' #12'
        );
        $this->assertTrue(
            $asit->tagExists( $tags[0] ),
            __FUNCTION__ . ' #13'
        );

        foreach( array_keys( $asit->tagGet( $tags[0] )) as $tIx ) {
            $asit->seek( $tIx );
            $asit->removeCurrentTag( $tags[0] );
        }
        $this->assertEquals(
            0,
            $asit->tagCount( $tags[0] ),
            __FUNCTION__ . ' #14'
        );
        $this->assertFalse(
            $asit->tagExists( $tags[0] ),
            __FUNCTION__ . ' #15'
        );

        $asit = null;
    }

    /**
     * Testing Asittag selecting on tags
     *
     * @test
     */
    public function asitTest75() : void
    {
        $asit = new Asittag();
        foreach( self::arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for
        $asit->seek( array_rand( array_flip( range( 0, 99 ))));

        $BIG       = 'big';
        $FAST      = 'fast';
        $ECOLOGIC  = 'ecologic';
        $CHEAP     = 'sheap';
        $ABRAND    = 'brand A';
        $LOWQUAL   = 'low quality';
        foreach(   $asit   as $key => $value ) { // 'internal key', NOT pKey
            if( 0 === $key % 10 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $FAST );
            }
            if( 0 === $key % 6 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $BIG );
            }
            if( 0 === $key % 5 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $CHEAP );
            }
            if( 0 === $key % 4 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ABRAND );
            }
            if( 0 === $key % 3 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $LOWQUAL );
            }
            if( 0 === $key % 2 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ECOLOGIC );
            }
        } // end foreach
        $selection = $asit->tagGet( $FAST );
        $this->assertCount(
            10, $selection, __FUNCTION__ . ' #1 exp 10 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG );
        $this->assertCount(
            17, $selection, __FUNCTION__ . ' #2 exp 17 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG, null, $FAST );
        $this->assertCount(
            13, $selection, __FUNCTION__ . ' #3 exp 13 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [$FAST, $ECOLOGIC], true );
        $this->assertCount(
            10, $selection, __FUNCTION__ . ' #4 exp 10 : got : ' . count( $selection )
        );

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], true, $BIG );
        $this->assertCount(
            6, $selection, __FUNCTION__ . ' #5 exp 6 : got : ' . count( $selection )
        );

        /*
        foreach( array_keys( $selection ) as $kIx ) {
            $asit->seek( $kIx );
            echo $kIx . ' '  . implode( ',', $asit->getCurrentTags()) . PHP_EOL; // test ###
        }
        */

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false );
        $this->assertCount(
            50, $selection, __FUNCTION__ . ' #7 exp 50 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false, [ $BIG, $CHEAP ] );
        $this->assertCount(
            27, $selection, __FUNCTION__ . ' #8 exp 17 : got : ' . count( $selection )
        );

        $asit = null;
    }

    /**
     * Testing Asittag remove, last, previous....
     *
     * @test
     */
    public function asitTest76() : void
    {
        $asit = new Asittag();
        foreach( self::arrayLoader( 5 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, 'tag' . $key );
        } // end for

        $asit->last();
        $asit->previous();
        $asit->previous();
        $key = $asit->key();
        $this->assertEquals( 2, $key, __FUNCTION__ . '-1, exp 2, got: ' . $key );

        $pKey = $asit->getCurrentPkey();
        $tags = $asit->getCurrentTags();

        $asit->remove();
        // check that pkey NOT exists
        $this->assertEmpty(
            $asit->pKeyGet( $pKey ),
            __FUNCTION__ . '-2, pkey: ' . $pKey . ', exp none, got ' . var_export( $asit->pKeyGet( $pKey ), true )
        );
        // check that tag NOT exists
        foreach( $tags as $tag ) {
            $this->assertFalse(
                $asit->tagExists( $tag ),
                __FUNCTION__ . '-3, exp none, got true'
            );
        }
        // remove all from the end
        $this->assertEquals( 4, $asit->count(), __FUNCTION__ . '-3, exp 4, got: ' . $asit->count());
        $asit->last();
        $asit->remove();
        $asit->previous();
        $asit->remove();
        $asit->previous();
        $asit->remove();
        $asit->previous();
        $asit->remove();
        $this->assertEquals( 0, $asit->count(), __FUNCTION__ . '-4, exp 0, got: ' . $asit->count());
    }

}
