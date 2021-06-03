<?php
/**
 * Asit package manages assoc arrays
 *
 * This file is part of Asit.
 *
 * Support <https://github.com/iCalcreator/Asit>
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @version   1.6
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
namespace Kigkonsult\Asit;

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class Asit2Test extends TestCase
{
    public function arrayLoader( $max = 1000 )
    {
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

    public static function getAttribute( $index )
    {
        $cIx = $index % 10;
        return self::$COLORS[$cIx];
    }

    /**
     * Testing Asittag 'current' methods :
     *    getCurrentTags, hasCurrentTag, addCurrentTag,
     *
     * @test
     */
    public function asitTest32()
    {

        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end foreach
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

        $asit->seek( array_rand( array_flip( range( 30, 39 )))); // set valid current

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

        $asit = null;
    }

    /**
     * Testing Asittag other primary key + tag methods
     *
     * @test
     */
    public function asitTest37()
    {
        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
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

        $tags1 = $asit->getTags( $pKey1 );
        $this->assertEquals(
            [ $ix1 => 'element' . $ix1 ],
            $asit->get( $search, $tags1 ),  // get( pKeys, tags )
            'test37-10 search : ' .
            implode( ',', $search ) . ' tags : ' . implode( ',', $tags1 ) .
            ' got : ' . implode( ',', $asit->get( $search, $tags1 ))
        );

        $this->assertEquals(
            [ $ix2 => 'element' . $ix2 ], $asit->get( $search, $asit->getTags( $pKey2 ) ),  // get( pKeys, tags )
            'test37-11'
        );
        $tags = array_merge(
            $asit->getTags( $pKey1 ),
            $asit->getTags( $pKey2 )
        );
        $this->assertEquals(
            [], $asit->get( $search, $tags, true ),  // get( pKeys, tags )
            'test37-12'
        );
        $this->assertEquals(
            [  $ix1 => 'element' . $ix1, $ix2 => 'element' . $ix2 ],
            $asit->get( $search, $tags, false ),  // get( pKeys, tags )
            'test37-13'
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
        $this->assertTrue(
            in_array( $newTag, $asit->getCurrentTags()),  // getCurrentTags
            'test37-14'
        );

        $newPkey = 'key9876';
        $tags    = $asit->getTags( $orgPkey );
        $asit->replacePkey( $orgPkey, $newPkey ); // alter pKey, replacePkey, ok
        $this->assertFalse(
            $asit->pKeyExists( $orgPkey ),
            'test37-18'
        );

        $orgIndex2 = $asit->getPkeyIndexes( [ $newPkey ] );
        $orgIndex1 = reset( $orgIndex2 );
        $this->assertEquals(
            $asit->key(), $orgIndex1,
            'test37-19 exp : ' . $asit->key() . ', got : ' . implode( ',', $orgIndex2 )
        );

        $this->assertEquals(
            $newPkey,
            $asit->getCurrentPkey(),
            'test37-20 current ix :' . $asit->key()
        );
        $this->assertEquals(
            [ $orgIndex => $current ],
            $asit->get( $newPkey ),
            'test37-21'
        );
        $this->assertEquals(
            $tags, $asit->getTags( $newPkey ),
            'test37-22'
        );

        $orgPkey = $asit->getCurrentPkey();
        $newPkey = 'testbcdefg';
        $asit->setCurrentPkey( $newPkey ); // alter pKey, replacePkey, ok
        $this->assertTrue(
            $orgPkey != $asit->getCurrentPkey(),
            'test37-23'
        );
        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            'test37-24'
        );

        $asit = null;
    }

    /**
     * Testing Asit/Asmit replacePkey and reuse key
     *
     * @test
     */
    public function asitTest41()
    {
        foreach(
            [ new Asit( $this->arrayLoader( 10 )), new Asmit( $this->arrayLoader( 10 )) ]
            as $tx => $asit ) {

            $asit->seek( array_rand( array_flip( range( 0, 8 ))));
            $pKey1   = $asit->getCurrentPkey();
            $ix1     = $asit->key();

            $asit->replacePkey( $pKey1, $pKey1 ); // replace by itself - test ??
            $orgIndex2 = array_values( $asit->getPkeyIndexes( [ $pKey1 ] ));
            $this->assertEquals(
                [ $ix1 ],
                $orgIndex2,
                'test41-1-' . $tx . ' org : ' . $ix1 . ', org2 : ' . var_export( $orgIndex2, true )
            );

            $newPkey = 'testbcdefg';

            $asit->replacePkey( $pKey1, $newPkey );
            $this->assertFalse(
                $asit->pKeyExists( $pKey1 ),
                'test41-2-' . $tx
            );
            $this->assertEquals(
                $ix1, $asit->key(),
                'test41-3-' . $tx
            );
            $this->assertEquals(
                $newPkey,
                $asit->getCurrentPkey(),
                'test41-4-' . $tx
            );

            $asit->next();
            $pKey2 = $asit->getCurrentPkey();
            $ix2   = $asit->key();
            $asit->replacePkey( $pKey2, $pKey1 );
            $this->assertFalse(
                $asit->pKeyExists( $pKey2 ),
                'test41-5-' . $tx
            );
            $this->assertEquals(
                $ix2, $asit->key(),
                'test41-6-' . $tx
            );
            $this->assertEquals(
                $pKey1,
                $asit->getCurrentPkey(),
                'test41-7-' . $tx
            );

            $asit = null;
        }
    }

    /**
     * Testing Assittag addPkeyTag + exception (not found pKey)
     *
     * @test
     */
    public function asitTest52()
    {
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
        $this->assertTrue( $ok == 2, 'test52-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag getCurrentTags + exception (no current)
     *
     * @test
     */
    public function asitTest53()
    {
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
        $this->assertTrue( $ok == 2, 'test53-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest54()
    {
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
        $this->assertTrue( $ok == 2, 'test54-1, exp 2, got ' . $ok );

        $asit = null;
    }

    /**
     * Testing Asittag addCurrentTag + exception (no current)
     *
     * @test
     */
    public function asitTest55()
    {
        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end foreach
        $asit->seek( array_rand( array_flip( range( 40, 44 ))));

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
        $this->assertTrue( $ok == 2, 'test55-6, exp 2, got ' . $ok );

        $newTag = 'newTag';
        $this->assertFalse(
            $asit->hasPkeyTag( 'fakePkey', $newTag ), // not found pKey
            'test55-7'
        );

        $pKey   = $asit->getCurrentPkey();
        $tags   = $asit->getCurrentTags();
        // position somewhere else
        $asit->seek( array_rand( array_flip( range( 45, 49 ))));

        $asit->addPkeyTag( $pKey, $newTag );
        $tags[] = $newTag;
        foreach( $tags as $testTag ) {
            $this->assertTrue(
                $asit->hasPkeyTag( $pKey, $testTag ),
                'test55-8'
            );
        }
        $this->assertfalse(
            $asit->hasPkeyTag( $pKey, 'fakeTag' ),
            'test55-9'
        );

        $asit = null;
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest62()
    {

        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for
        $asit->seek( array_rand( array_flip( range( 50, 59 ))));

        $currentValue = $asit->current();
        $currentags   = $asit->getCurrentTags();   // test getCurrentTags
        $currentPkey  = $asit->getCurrentPkey();
        $pKeytags1    = $asit->getTags( $currentPkey );   // test getTags( $pKey )
        $this->assertEquals(
            $currentags, $pKeytags1,
            'test62-1 - currenTags : ' . implode( ',', $currentags ) . ' <-> pKeytags1 : ' . implode( ',', $pKeytags1 )
        );
        $this->assertTrue(
            [] == $asit->getTags( 'fake pKey' ),
            'test62-2'
        );
        $pKeytags2   = $asit->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertEquals(
            $pKeytags1, $pKeytags2,
            'test62-3 - tags1 : ' . implode( ',', $pKeytags1 ) . ' <-> tags2 : ' . implode( ',', $pKeytags2 )
        );

        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $pKeytags1[0] )), // getPkeys( tag )
            'test62-5'
        );
        $this->assertTrue(
            [] == $asit->getPkeys( 'fakeTag' ),
            'test62-6'
        );

        $allTags = $asit->getTags();
        foreach( self::$COLORS as $attribute ) {
            $this->assertTrue(
                in_array( $attribute, $allTags ),
                'test62-7'
            );
            $attrCnt = $asit->tagCount( $attribute ); // test tagCount( <tag> )
            $this->assertTrue(
                10 == $attrCnt,
                'test62-8 - tag : ' . $attribute . ', count: ' . $attrCnt
            );
        }
        $elements1 = $asit->get( null, $currentags ); // test get( null, $tags1 ) : all with currentags
        $this->assertTrue(
            in_array( $currentValue, $elements1 ),
            'test62-9 exp : ' . $currentValue . ', got ' . var_export( $elements1, true )
        );
        $this->assertTrue(
            10 == count( $elements1 ),
            'test62-10 exp : 100, got ' . count( $elements1 )
        );

        $asit = null;
    }

    /**
     * Testing tag - getTags method
     *
     * @test
     */
    public function asitTest65()
    {
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
                'test65-2-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }
        foreach( $asit->getTags() as $tIx => $tag ) {
            // case-sensitively sort
            $exp = ( $tIx < 5 ) ? 'TAG' : 'tag';
//          echo ' 2 got : ' . $tag . PHP_EOL;
            $this->assertEquals(
                $exp,
                substr( $tag, 0, 3 ),
                'test65-3-' . $tIx . ', exp : ' . $exp . ', got : ' . $tag
            );
        }

        $asit = null;
    }

    /**
     * Testing Asittag tag
     *
     * @test
     */
    public function asitTest71()
    {
        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
        } // end for
        $asit->seek( array_rand( array_flip( range( 61, 69 ))));

        $currentPkey = $asit->getCurrentPkey();
        $lightGray   = 'lightGray';
        $deepBlue    = 'deepBlue';
        $asit->addPkeyTag( $currentPkey, $lightGray );  //addPkeyTag
        $asit->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag
        $asit->addPkeyTag( $currentPkey, $deepBlue );  //addPkeyTag, duplicates eliminated
        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $lightGray )), // getPkeys( tag )
            'test71-1 exp : ' . $currentPkey . ', got ' . implode( ',', $asit->getPkeys( $lightGray )) .
            ', search on tag ' . $lightGray . ', current has ' . implode( ',', $asit->getCurrentTags())
        );
        $this->assertTrue(
            in_array( $currentPkey, $asit->getPkeys( $deepBlue )), // getPkeys( tag )
            'test71-2 exp : ' . $currentPkey . ', got ' . implode( ',', $asit->getPkeys( $deepBlue )) .
            ', search on tag ' . $deepBlue . ', current has ' . implode( ',', $asit->getCurrentTags())
        );

        $lightBlue  = 'lightBlue';
        $asit->addCurrentTag( $lightBlue );

        $numericTag = 1234;
        $asit->addCurrentTag( $numericTag );

        $pKeytags3   = $asit->getPkeyTags( $currentPkey );   // test getPkeyTags( $currentPkey ) getTags alias
        $this->assertTrue(
            5 == count( $pKeytags3 ),
            'test71-3'
        );
        $this->assertTrue(
            in_array( $lightGray, $pKeytags3 ),
            'test71-4'
        );
        $this->assertTrue(
            in_array( $deepBlue, $pKeytags3 ),
            'test71-5'
        );
        $this->assertTrue(
            in_array( $lightBlue, $pKeytags3 ),
            'test71-6'
        );
        $this->assertTrue(
            in_array( $numericTag, $pKeytags3 ),
            'test71-7'
        );

        $current   = $asit->current(); // current value
        $currentIx = $asit->key();
        $this->assertEquals(
            [ $currentIx => $current ], $asit->tagGet( $lightBlue ),
            'test71-8'
        );
        $this->assertEquals(
            [ $currentIx => $current ], $asit->tagGet( $pKeytags3, true ),
            'test71-9'
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
            'test71-20'
        );

        $this->assertEquals(
            $newPkey, $asit->getCurrentPkey(),
            'test71-21'
        );

        foreach( $newTags as $tag ) {
            $this->assertTrue(
                $asit->hasCurrentTag( $tag ),  // hasCurrentTag
                'test71-22 current has : ' . implode( ',', $asit->getCurrentTags()) . ' exp : ' . $tag
            );
        }
        $this->assertFalse(
            $asit->hasCurrentTag( 'fake' ),
            'test71-23'
        );

        $newTags[] = 'fake'; // no one has 'fake', will be skipped in get/tagGet
        $this->assertCount(
            2, $asit->tagGet( $newTags, true ), // tagGet
            'test71-24'
        );
        $this->assertEquals(
            [ $currentIx => $current, $newIx => $newElement ],
            $asit->tagGet( $newTags, true ), // tagGet
            'test71-25'
        );

        $asit = null;
    }

    /**
     * Testing Asittag removePkeyTag
     *
     * @test
     */
    public function asitTest72()
    {
        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
            $asit->append( $value, $key );
            $asit->addPkeyTag( $key, self::getAttribute( $asit->key() ) );
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
        $this->assertTrue( $ok == 1, 'test72-1, exp 1, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( 'fakePkey', $asit->getCurrentTags()[0] );
            $ok = 1;
        } catch( InvalidArgumentException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test72-2, exp 2, got ' . $ok );

        $ok   = 0;
        try {
            $asit->removePkeyTag( $asit->getCurrentPkey(), 'fakeTag' );
            $ok = 1;
        } catch( RuntimeException $e ) {
            $ok = 2;
        } catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 1, 'test72-3, exp 1, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 62, 66 ))));

        $pKey = $asit->getCurrentPkey();
        $tags = $asit->getPkeyTags( $pKey );
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test72-4'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // but tag exists
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test72-5'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test72-6'
        );
        $asit->removePkeyTag( $pKey, $tags[0] ); // tag don't exists, no exception
        $this->assertFalse(
            $asit->hasPkeyTag( $pKey, $tags[0] ),
            'test72-7'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test72-8'
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
        $this->assertTrue( $ok == 2, 'test72-9, exp 2, got ' . $ok );

        $asit->seek( array_rand( array_flip( range( 67, 69 ))));

        $tags = $asit->getCurrentTags();
        $tCnt = $asit->tagCount( $tags[0] );
        $this->assertTrue(
            $asit->hasCurrentTag( $tags[0] ),
            'test72-10, exp : ' . $tags[0]
        );
        $asit->removeCurrentTag( $tags[0] );
        $this->assertFalse(
            $asit->hasCurrentTag( $tags[0] ),
            'test72-11'
        );
        $this->assertEquals(
            ( $tCnt - 1 ),
            $asit->tagCount( $tags[0] ),
            'test72-12'
        );
        $this->assertTrue(
            $asit->tagExists( $tags[0] ),
            'test72-13'
        );

        foreach( array_keys( $asit->tagGet( $tags[0] )) as $tIx ) {
            $asit->seek( $tIx );
            $asit->removeCurrentTag( $tags[0] );
        }
        $this->assertEquals(
            0,
            $asit->tagCount( $tags[0] ),
            'test72-14'
        );
        $this->assertFalse(
            $asit->tagExists( $tags[0] ),
            'test72-15'
        );

        $asit = null;
    }

    /**
     * Testing Asittag selecting on tags
     *
     * @test
     */
    public function asitTest75()
    {
        $asit = new Asittag();
        foreach( $this->arrayLoader( 100 ) as $key => $value ) {
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
            if( 0 == $key % 10 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $FAST );
            }
            if( 0 == $key % 6 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $BIG );
            }
            if( 0 == $key % 5 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $CHEAP );
            }
            if( 0 == $key % 4 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ABRAND );
            }
            if( 0 == $key % 3 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $LOWQUAL );
            }
            if( 0 == $key % 2 ) {
                $asit->seek( $key );
                $asit->addCurrentTag( $ECOLOGIC );
            }
        } // end foreach
        $selection = $asit->tagGet( $FAST );
        $this->assertTrue(
            10 == count( $selection ),
            'test75-1 exp 10 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG );
        $this->assertTrue(
            17 == count( $selection ),
            'test75-2 exp 17 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( $BIG, null, $FAST );
        $this->assertTrue(
            13 == count( $selection ),
            'test75-3 exp 13 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [$FAST, $ECOLOGIC], true );
        $this->assertTrue(
            10 == count( $selection ),
            'test75-4 exp 10 : got : ' . count( $selection )
        );

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], true, $BIG );
        $this->assertTrue(
            6 == count( $selection ),
            'test75-5 exp 6 : got : ' . count( $selection )
        );

        /*
        foreach( array_keys( $selection ) as $kIx ) {
            $asit->seek( $kIx );
            echo $kIx . ' '  . implode( ',', $asit->getCurrentTags()) . PHP_EOL; // test ###
        }
        */

        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false );
        $this->assertTrue(
            50 == count( $selection ),
            'test75-7 exp 50 : got : ' . count( $selection )
        );
        $selection = $asit->tagGet( [ $FAST, $ECOLOGIC ], false, [ $BIG, $CHEAP ] );
        $this->assertTrue(
            27 == count( $selection ),
            'test75-8 exp 17 : got : ' . count( $selection )
        );

        $asit = null;
    }

    /**
     * Test Asittag append Pkey + InvalidArgumentException, invaid tag
     *
     * @test
     */
    public function asitTest82()
    {
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

        $asit = null;
    }
}
