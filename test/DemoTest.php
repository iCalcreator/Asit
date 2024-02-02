<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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

use DateTime;
use PHPUnit\Framework\TestCase;

class MyClassIt           extends It {}
class MyClassItList       extends ItList {}
class MyClassAsit         extends Asit {}
class MyClassAsitList     extends AsitList {}
class MyClassAsmit        extends Asmit {}
class MyClassAsmitList    extends AsmitList {}
class MyClassAsittag      extends Asittag {}
class MyClassAsittagList  extends AsittagList {}
class MyClassAsmittag     extends Asmittag {}
class MyClassAsmittagList extends AsmittagList {}

/**
 * The demo/smoke-test
 */
class DemoTest extends TestCase
{
    /**
     * @var string
     */
    private static string $VALUE = 'value';

    /**
     * @var string
     */
    private static $PKEY         = 'pkey';

    /**
     * @var string
     */
    private static $TAG          = 'tag';

    /**
     * @test It demo test
     */
    public function itTest1() : void
    {
        $myClass = MyClassIt::factory();
        $myClass->append( self::$VALUE . 1 );
        $myClass->setCollection( [ self::$VALUE . 2, self::$VALUE . 3 ] );

        $cnt = 0;
        $myClass->rewind();
        while( $myClass->valid()) {
            ++$cnt;
            $this->assertEquals( self::$VALUE . $cnt, $myClass->current());
            $myClass->next();
        } // end while

        $cnt = 4;
        $myClass->last();
        while( $myClass->valid()) {
            --$cnt;
            $this->assertEquals( self::$VALUE . $cnt, $myClass->current());
            $myClass->previous();
        } // end while
    }

    /**
     * @test ItList demo test
     */
    public function itListTest() : void
    {
        $myClass = MyClassItList::factory( DateTime::class );
        $value   = new DateTime();
        $myClass->append( $value);
        $this->assertEquals( $value, $myClass->current());
    }

    /**
     * @test Asit demo test
     */
    public function AsitTest() : void
    {
        $myClass = MyClassAsit::factory();
        $myClass->append( self::$VALUE . 1, self::$PKEY . 1 );
        $myClass->append( self::$VALUE . 2, self::$PKEY . 2 );
        $myClass->append( self::$VALUE . 3, self::$PKEY . 3 );
        $output = $myClass->pKeyGet( self::$PKEY . 2 );
        $this->assertEquals( self::$VALUE . 2, reset( $output ));
    }

    /**
     * @test AsitList demo test
     */
    public function asitListTest() : void
    {
        $myClass = MyClassAsitList::factory( DateTime::class );
        $value   = new DateTime();
        $myClass->append( $value);
        $this->assertEquals( $value, $myClass->current());
    }

    /**
     * @test Asit demo test
     */
    public function asmitTest() : void
    {
        $myClass = MyClassAsmit::factory();
        $myClass->append( self::$VALUE . 1, self::$PKEY . 11 )
            ->addCurrentPkey( self::$PKEY . 12 );
        $myClass->append( self::$VALUE . 2, self::$PKEY . 21 )
            ->addCurrentPkey( self::$PKEY . 22 );
        $myClass->append( self::$VALUE . 3, self::$PKEY . 31 )
            ->addCurrentPkey( self::$PKEY . 32 );
        $output = $myClass->pKeyGet( self::$PKEY . 22 );
        $this->assertEquals( self::$VALUE . 2, reset( $output ));
    }

    /**
     * @test AsmitList demo test
     */
    public function asmitListTest() : void
    {
        $myClass = MyClassAsmitList::factory( DateTime::class );
        $value1  = new DateTime( '2024-01-11' );
        $myClass->append( $value1, self::$PKEY . 11 )
            ->addCurrentPkey( self::$PKEY . 12 );
        $value2  = new DateTime( '2024-01-12' );
        $myClass->append( $value2, self::$PKEY . 21 )
            ->addCurrentPkey( self::$PKEY . 22 );
        $value3  = new DateTime( '2024-01-13' );
        $myClass->append( $value3, self::$PKEY . 31 )
            ->addCurrentPkey( self::$PKEY . 32 );
        $output = $myClass->pKeyGet( self::$PKEY . 22 );
        $this->assertEquals( $value2, reset( $output ));
    }

    /**
     * @test Asittag demo test
     */
    public function asittagTest() : void
    {
        $myClass = MyClassAsittag::factory();
        $myClass->append( self::$VALUE . 1, self::$PKEY . 1, self::$TAG . 11 )
            ->addCurrentTag( self::$TAG . 12 );
        $myClass->append( self::$VALUE . 2, self::$PKEY . 2, self::$TAG . 21 )
            ->addCurrentTag( self::$TAG . 22 );
        $myClass->append( self::$VALUE . 3, self::$PKEY . 3, self::$TAG . 31 )
            ->addCurrentTag( self::$TAG . 32 );
        $output = $myClass->pKeyTagGet( null, self::$TAG . 22 );
        $this->assertEquals( self::$VALUE . 2, reset( $output ));
    }

    /**
     * @test AsittagList demo test
     */
    public function asittagListTest() : void
    {
        $myClass = MyClassAsittagList::factory( DateTime::class );
        $value1  = new DateTime( '2024-01-11' );
        $myClass->append( $value1, self::$PKEY . 1, self::$TAG . 11 )
            ->addCurrentTag( self::$TAG . 12 )
            ->addCurrentTag( 1 );
        $value2  = new DateTime( '2024-01-12' );
        $myClass->append( $value2, self::$PKEY . 2, self::$TAG . 21 )
            ->addCurrentTag( self::$TAG . 22 )
            ->addCurrentTag( 2 );
        $value3  = new DateTime( '2024-01-13' );
        $myClass->append( $value3, self::$PKEY . 3, self::$TAG . 31 )
            ->addCurrentTag( self::$TAG . 32 )
            ->addCurrentTag( 1 );
        $output = $myClass->pKeyTagGet( null, self::$TAG . 22 );
        $this->assertEquals( $value2, reset( $output ));
        $this->assertCount( 2, $myClass->pKeyTagGet( null, 1 ));
    }

    /**
     * @test Asittag demo test
     */
    public function asmittagTest() : void
    {
        $myClass = MyClassAsmittag::factory();
        $myClass->append( self::$VALUE . 1, self::$PKEY . 11, self::$TAG . 11 )
            ->addCurrentPkey( self::$PKEY . 12 )
            ->addCurrentTag( 1 );
        $myClass->append( self::$VALUE . 2, self::$PKEY . 21, self::$TAG . 21 )
            ->addCurrentPkey( self::$PKEY . 22 )
            ->addCurrentTag( 2 );
        $myClass->append( self::$VALUE . 3, self::$PKEY . 31, self::$TAG . 31 )
            ->addCurrentPkey( self::$PKEY . 32 )
            ->addCurrentTag( 2 );
        $output = $myClass->pKeyGet( self::$PKEY . 22 );
        $this->assertEquals( self::$VALUE . 2, reset( $output ));
        $this->assertCount( 1, $myClass->pKeyTagGet( null, 1 ) );
    }

    /**
     * @test AsmittagList demo test
     */
    public function asmittagListTest() : void
    {
        $myClass = MyClassAsmittagList::factory( DateTime::class );
        $value1  = new DateTime( '2024-01-11' );
        $myClass->append( $value1, self::$PKEY . 11, self::$TAG . 11 )
            ->addCurrentPkey( self::$PKEY . 12 )
            ->addCurrentTag( self::$TAG . 12 )
            ->addCurrentTag( 1 );
        $value2  = new DateTime( '2024-01-12' );
        $myClass->append( $value2, self::$PKEY . 21, self::$TAG . 21 )
            ->addCurrentPkey( self::$PKEY . 22 )
            ->addCurrentTag( self::$TAG . 22 )
            ->addCurrentTag( 2 );
        $value3  = new DateTime( '2024-01-13' );
        $myClass->append( $value3, self::$PKEY . 31, self::$TAG . 31 )
            ->addCurrentPkey( self::$PKEY . 32 )
            ->addCurrentTag( self::$TAG . 32 )
            ->addCurrentTag( 1 );
        $output = $myClass->pKeyTagGet( null, self::$TAG . 22 );
        $this->assertEquals( $value2, reset( $output ));
        $this->assertCount( 2, $myClass->pKeyTagGet( null, 1 ) );
    }
}
