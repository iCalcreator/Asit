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
use ReflectionClass;
use stdClass;

/**
 * Class ConcurrentTest
 *
 * test
 *   concurrent It/Asit/Asmit/Asittag/Asmittag/asmittagList instances
 *   Asmittag/asmittagList singleton instances
 *   clone
 *
 * @since 2.2.9
 */
class ConcurrentTest extends TestCase
{
    private static string $TEST = 'test';
    private static string $KEY  = 'key';
    private static string $TAG  = 'tag';

    /**
     * Test It instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function itTest(): void
    {
        $aClass = new It();
        $aClass->append( self::$TEST . 1 )
            ->append( self::$TEST . 2 )
            ->append( self::$TEST . 3 );
        $bClass = new It();
        $bClass->append( self::$TEST . 1 )
            ->append( self::$TEST . 2 )
            ->append( self::$TEST . 3 );

        $aClass->rewind()
            ->next()
            ->remove();
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 1');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 2');

        $bClass->append( self::$TEST . 4 );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 3');
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 4');

        $bClass->seek( 2 )
            ->remove();
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 5');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 6');

        $aClass->init();
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 7');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 8');
    }

    /**
     * Test Asit instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asitTest(): void
    {
        $aClass = new Asit();
        $aClass->append( self::$TEST . 1, self::$KEY . 1 )
            ->append( self::$TEST . 2, self::$KEY . 2 )
            ->append( self::$TEST . 3, self::$KEY . 3 );
        $bClass = new Asit();
        $bClass->append( self::$TEST . 1, self::$KEY . 1 )
            ->append( self::$TEST . 2, self::$KEY . 2 )
            ->append( self::$TEST . 3, self::$KEY . 3 );

        $aClass->pKeySeek( self::$KEY . 2 )
            ->remove();
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 1' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 2' );

        $bClass->append( self::$TEST . 4, self::$KEY . 4 );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 3' );
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 4' );

        $bClass->pKeySeek( self::$KEY . 3 )
            ->remove();
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 5' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 6' );

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 1 ), __METHOD__ . ' 11' );
        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 12' );
        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 3 ), __METHOD__ . ' 13' );

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 1 ), __METHOD__ . ' 21' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 22' );
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 3 ), __METHOD__ . ' 23' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 4 ), __METHOD__ . ' 24' );

        $aClass->init();
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 31' );
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 32' );
    }

    /**
     * Test Asmit instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asmitTest(): void
    {
        $aClass = new Asmit();
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 );
        $bClass = new Asmit();
        $bClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 );

        $aClass->pKeySeek( self::$KEY . 22 )
            ->remove();

        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 1');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 2');

        $bClass->append( self::$TEST . 4, self::$KEY . 14 )
            ->addCurrentPkey( self::$KEY . 24 );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 3');
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 4');

        $bClass->pKeySeek( self::$KEY . 23 )
            ->remove();

        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 5');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 6');

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 11');
        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 12');
        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 13');

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 21');
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 22');
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 23');
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 24 ), __METHOD__ . ' 24');

        $aClass->init();
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 31');
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 32');
    }

    /**
     * Test Asittag instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asittagTest(): void
    {
        $aClass = new Asittag();
        $aClass->append( self::$TEST . 1, self::$KEY . 1 )
            ->addCurrentTag( self::$TAG . 1 )
            ->append( self::$TEST . 2, self::$KEY . 2 )
            ->addCurrentTag( self::$TAG . 2 )
            ->append( self::$TEST . 3, self::$KEY . 3 )
            ->addCurrentTag( self::$TAG . 3 );
        $bClass = new Asittag();
        $bClass->append( self::$TEST . 1, self::$KEY . 1 )
            ->addCurrentTag( self::$TAG . 1 )
            ->append( self::$TEST . 2, self::$KEY . 2 )
            ->addCurrentTag( self::$TAG . 2 )
            ->append( self::$TEST . 3, self::$KEY . 3 )
            ->addCurrentTag( self::$TAG . 3 );

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 11');
        $this->assertTrue( $aClass->tagExists( self::$TAG . 2 ), __METHOD__ . ' 12');

        foreach( $aClass->getPkeys( self::$TAG . 2 ) as $pKey ) {
            if( $aClass->pKeyExists( $pKey )) {
                $aClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 13');
        $this->assertFalse( $aClass->tagExists( self::$TAG . 2 ), __METHOD__ . ' 14');
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 15' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 16' );

        $bClass->append( self::$TEST . 4, self::$KEY . 4 )
            ->addCurrentTag( self::$TAG . 4 );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 21' );
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 22' );

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 3 ), __METHOD__ . ' 23');
        $this->assertTrue( $bClass->tagExists( self::$TAG . 3 ), __METHOD__ . ' 24');

        foreach( $bClass->getPkeys( self::$TAG . 2 ) as $pKey ) {
            if( $bClass->pKeyExists( $pKey )) {
                $bClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 25');
        $this->assertFalse( $bClass->tagExists( self::$TAG . 2 ), __METHOD__ . ' 26');
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 27' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 28' );

        $this->assertTrue(  $aClass->pKeyExists( self::$KEY . 1 ), __METHOD__ . ' 31' );
        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 32' );
        $this->assertTrue(  $aClass->pKeyExists( self::$KEY . 3 ), __METHOD__ . ' 33' );

        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 1 ), __METHOD__ . ' 41' );
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 2 ), __METHOD__ . ' 42' );
        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 3 ), __METHOD__ . ' 43' );
        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 4 ), __METHOD__ . ' 44' );

        $aClass->init();
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 51' );
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 52' );
    }

    /**
     * Test Asmittag instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asmittagTest1(): void
    {
        $aClass = new Asmittag();
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );
        $bClass = new Asmittag();
        $bClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12)
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 11' );
        $this->assertTrue( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 12' );

        foreach( $aClass->getPkeys( self::$TAG . 12 ) as $pKey ) {
            if( $aClass->pKeyExists( $pKey )) {
                $aClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 13' );
        $this->assertFalse( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 14' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 15' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 16');

        $bClass->append( self::$TEST . 4, self::$KEY . 14 )
            ->addCurrentPkey( self::$KEY . 24 )
            ->addCurrentTag( self::$TAG . 4 );

        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 21');
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 22');

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 23' );
        $this->assertTrue( $bClass->tagExists( self::$TAG . 13 ), __METHOD__ . ' 24' );

        foreach( $bClass->getPkeys( self::$TAG . 13 ) as $pKey ) {
            if( $bClass->pKeyExists( $pKey )) {
                $bClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 25' );
        $this->assertFalse( $bClass->tagExists( self::$TAG . 13 ), __METHOD__ . ' 26' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 27');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 28');

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 31' );
        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 32' );
        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 33' );

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 41' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 42' );
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 43' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 24 ), __METHOD__ . ' 44' );

        $aClass->init();
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 51' );
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 52' );
    }

    /**
     * Reset singleton instances, i.e. make the 'instances' property empty
     *
     * @see https://coderwall.com/p/tx9cgg/resetting-singletons-in-php-testing-the-untestable
     * @param BaseInterface $classInstance
     * @return void
     */
    protected static function resetSingleton( BaseInterface $classInstance ) : void
    {
        static $propName = 'instances';
        $reflection = new ReflectionClass( $classInstance );
        $instance   = $reflection->getProperty( $propName );
        $instance->setAccessible( true );   // now we can modify that :)
        $instance->setValue( null, [] ); // instance is gone
        $instance->setAccessible( false );  // clean up
    }

    /**
     * Test Asmittag singleton instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asmittagTest2(): void
    {
        self::resetSingleton( Asmittag::getInstance());
        $aClass = Asmittag::getInstance();
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );
        $bClass = Asmittag::getInstance();
        $cClass = Asittag::getInstance();

        $this->assertEquals( 3, $aClass->count(), __METHOD__ . ' 11' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 12' );
        $this->assertEquals( 0, $cClass->count(), __METHOD__ . ' 13' );

        foreach( $aClass->getPkeys( self::$TAG . 12 ) as $pKey ) {
            if( $aClass->pKeyExists( $pKey )) {
                $aClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 21' );
        $this->assertFalse( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 22' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 23' );
        $this->assertEquals( 2, $bClass->count(), __METHOD__ . ' 24');
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 25' );
        $this->assertFalse( $bClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 26' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 27' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 28' );

        $aClass->init();
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 51' );
        $this->assertEquals( 0, $bClass->count(), __METHOD__ . ' 52' );
    }

    /**
     * Test AsmittagList instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asmittagListTest1(): void
    {
        $aClass = new AsmittagList( AsmittagList::STRING );
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );
        $bClass = new AsmittagList( AsmittagList::STRING );
        $bClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12)
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );

        $this->assertTrue( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 11' );
        $this->assertTrue( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 12' );
        foreach( $aClass->getPkeys( self::$TAG . 12 ) as $pKey ) {
            if( $aClass->pKeyExists( $pKey )) {
                $aClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 13' );
        $this->assertFalse( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 14' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 15' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 16');

        $bClass->append( self::$TEST . 4, self::$KEY . 14 )
            ->addCurrentPkey( self::$KEY . 24 )
            ->addCurrentTag( self::$TAG . 4 );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 21');
        $this->assertEquals( 4, $bClass->count(), __METHOD__ . ' 22');

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 23' );
        $this->assertTrue( $bClass->tagExists( self::$TAG . 13 ), __METHOD__ . ' 24' );
        foreach( $bClass->getPkeys( self::$TAG . 13 ) as $pKey ) {
            if( $bClass->pKeyExists( $pKey )) {
                $bClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 25' );
        $this->assertFalse( $bClass->tagExists( self::$TAG . 13 ), __METHOD__ . ' 26' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 27');
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 28');

        $this->assertTrue(  $aClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 31' );
        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 32' );
        $this->assertTrue(  $aClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 33' );

        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 41' );
        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 42' );
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 43' );
        $this->assertTrue(  $bClass->pKeyExists( self::$KEY . 24 ), __METHOD__ . ' 44' );

        $aClass->init();
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 51' );
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 52' );
    }

    /**
     * Test AsmittagList clone 1
     *
     * @test
     *
     * @return void
     */
    public function asmittagListTest2(): void
    {
        $aClass = new AsmittagList( AsmittagList::STRING );
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );

        $bClass = clone $aClass;

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 11' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 12' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 13' );

        $this->assertEquals( $aClass, $bClass, __METHOD__ . ' 21');
    }

    /**
     * Test AsmittagList clone 2
     *
     * @test
     *
     * @return void
     */
    public function asmittagListTest3(): void
    {
        $aClass = new AsmittagList( stdClass::class );

        $element = new stdClass();
        $element->property = 1;
        $aClass->append( $element, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 );

        $element = new stdClass();
        $element->property = 2;
        $aClass->append( $element, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 );

        $element = new stdClass();
        $element->property = 3;
        $aClass->append( $element, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );

        $bClass = clone $aClass->rewind();

        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 11' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 12' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 13' );

        $this->assertEquals( $aClass->rewind(), $bClass->rewind(), __METHOD__ . ' 21');
    }

    /**
     * Test AsmittagList instances concurrency
     *
     * @test
     *
     * @return void
     */
    public function asmittagListTest4(): void
    {
        self::resetSingleton( AsmittagList::getInstance( AsmittagList::STRING ));
        $aClass = AsmittagList::getInstance( AsmittagList::STRING );
        $aClass->append( self::$TEST . 1, self::$KEY . 11 )
            ->addCurrentPkey( self::$KEY . 21 )
            ->addCurrentTag( self::$TAG . 11 )
            ->append( self::$TEST . 2, self::$KEY . 12 )
            ->addCurrentPkey( self::$KEY . 22 )
            ->addCurrentTag( self::$TAG . 12 )
            ->append( self::$TEST . 3, self::$KEY . 13 )
            ->addCurrentPkey( self::$KEY . 23 )
            ->addCurrentTag( self::$TAG . 13 );
        $bClass = AsmittagList::getInstance( AsmittagList::STRING );
        $cClass = AsmittagList::getInstance( AsmittagList::INT );
        $dClass = AsittagList::getInstance( AsmittagList::STRING );

        $this->assertEquals( 3, $aClass->count(), __METHOD__ . ' 11' );
        $this->assertEquals( 3, $bClass->count(), __METHOD__ . ' 12' );
        $this->assertEquals( 0, $cClass->count(), __METHOD__ . ' 13' );
        $this->assertEquals( 0, $dClass->count(), __METHOD__ . ' 14' );

        foreach( $aClass->getPkeys( self::$TAG . 12 ) as $pKey ) {
            if( $aClass->pKeyExists( $pKey )) {
                $aClass->pKeySeek( $pKey )->remove();
            }
        }

        $this->assertFalse( $aClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 21' );
        $this->assertFalse( $aClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 22' );
        $this->assertEquals( 2, $aClass->count(), __METHOD__ . ' 23' );
        $this->assertEquals( 2, $bClass->count(), __METHOD__ . ' 24');
        $this->assertFalse( $bClass->pKeyExists( self::$KEY . 22 ), __METHOD__ . ' 25' );
        $this->assertFalse( $bClass->tagExists( self::$TAG . 12 ), __METHOD__ . ' 26' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 21 ), __METHOD__ . ' 27' );
        $this->assertTrue( $bClass->pKeyExists( self::$KEY . 23 ), __METHOD__ . ' 28' );

        $aClass->init();
        $this->assertEquals( 0, $aClass->count(), __METHOD__ . ' 51' );
        $this->assertEquals( 0, $bClass->count(), __METHOD__ . ' 52' );
    }
}
