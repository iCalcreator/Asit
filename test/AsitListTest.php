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
use DateTime;
use DateTimeInterface;
use Exception;
use InvalidArgumentException;
use IteratorAggregate;
use Kigkonsult\Asit\Exceptions\TypeException;
use stdClass;
use Traversable;

interface TestIfc
{
    public const HALLO_WORLD = 'Hallo world';
}

class testClass2
{
    public function __invoke() : string
    {
        return 'Hallo world';
    }
}


class TestClass3 implements IteratorAggregate
{
    /**
     * @return Traversable
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator( [ 1, 2, 3 ] );
    }
}

class AsitListTest extends AsitBaseTest
{
    public static string $file1 = 'file1';
    public static string $file2 = 'file1';

    public static function setUpBeforeClass() : void
    {
        touch( self::$file1 );
        touch( self::$file2 );
    }

    public static function tearDownAfterClass() : void
    {
        if( is_file( self::$file1 )) {
            unlink( self::$file1 );
        }
        if( is_file( self::$file1 )) {
            unlink( self::$file2 );
        }
    }

    /**
     * test list::_construct/factory
     *
     * @test
     *
     */
    public function listTest11() : void
    {
        $data1  = [ 'key1' => 'value1' ];
        $data24 = [ 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4', ];
        $list1 = new AsmitList( $data1, AsmitList::STRING);
        foreach( $data24 as $key => $value ) {
            $list1->append( $value, $key );
        }
        $list2 = AsmitList::factory( $data1, AsmitList::STRING )
            ->append( 'value2', 'key2' )
            ->append( 'value3', 'key3' )
            ->append( 'value4', 'key4' );

        $list3 = AsmitList::factory( AsmitList::STRING )
            ->append( 'value1', 'key1' )
            ->append( 'value2', 'key2' )
            ->append( 'value3', 'key3' )
            ->append( 'value4', 'key4' );

        $this->assertEquals(
            $list1->get(),
            $list2->get(),
            __FUNCTION__ . ' #1'
        );

        $this->assertEquals(
            $list1->pKeyGet(),
            $list3->pKeyGet(),
            __FUNCTION__ . ' #2'
        );

        $this->assertEquals(
            $list1->getPkeyIterator(),
            $list2->getPkeyIterator(),
            __FUNCTION__ . ' #3'
        );

        $this->assertEquals(
            $list1->getPkeyIterator(),
            $list3->getPkeyIterator(),
            __FUNCTION__ . ' #4'
        );
    }

    /**
     * test not set valueType (assertValueType)
     *
     * @test
     */
    public function listTest2() : void
    {
        $ok    = 0;
        try {
            AsmittagList::factory();
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1 exp 2, got ' . $ok
        );
    }

    /**
     * test not set valueType (assertValueType)
     *
     * @test
     */
    public function listTest3() : void
    {
        $ok    = 0;
        try {
            AsmittagList::factory( AsmittagList::INT )
                ->assertElementType( 'value' );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2 exp 2, got ' . $ok
        );
    }

    /**
     * test not set valueType (assertValueType)
     *
     * @test
     */
    public function listTest4() : void
    {
        $ok    = 0;
        try {
            AsmittagList::factory( AsmittagList::INT )
                ->append( 'value' );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #3 exp 2, got ' . $ok
        );
    }

    /**
     * @test *List singleton
     */
    public function listTest9() : void
    {
        $list1  = AsmittagList::getInstance(
            $this->arrayListLoader( AsmittagList::STRING ),
            AsmittagList::STRING
        );
        $cnt1   = $list1->count();

        $list2  = AsmittagList::getInstance( AsmittagList::STRING );

        $this->assertEquals(
            $cnt1,
            $list2->count(),
            __FUNCTION__ . ' #1'
        );
        $this->assertEquals(
            AsmittagList::STRING,
            $list2->getValueType(),
            __FUNCTION__ . ' #2'
        );
    }

    /**
     * dataProvider
     *
     * @return array
     */
    public static function listLoader() : array
    {
        $testData = [];

        $testData[] = [
            1,
            new ItList( ItList::STRING )
        ];

        $testData[] = [
            2,
            new AsitList( ItList::STRING )
        ];

        $testData[] = [
            3,
            new AsmitList( ItList::STRING )
        ];

        $testData[] = [
            4,
            AsittagList::factory( ItList::STRING )
        ];

        $testData[] = [
            5,
            AsmittagList::factory( ItList::STRING )
        ];

        return $testData;
    }

    /**
     * test assertValueType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest12( int $case, mixed $list ) : void
    {
        $case += 10;
        $ok    = 0;
        try {
            $list->assertValueType( AsitList::ARRAY2 );
            $list->assertValueType( '12345' );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest20( int $case, mixed $list ) : void
    {
        $case += 200;
        $ok    = 0;
        try {
            $list->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertEquals(
            1, $ok, __FUNCTION__ . ' #' . $case . ', exp 1, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest21( int $case, mixed $list ) : void
    {
        $case += 210;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::ARRAY2 )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
        $ok = 0;
        try {
            $list->setValueType( AsitList::ARRAY2 )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest22( int $case, mixed $list ) : void
    {
        $case += 220;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::BOOL )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
        $ok = 0;
        try {
            $list->setValueType( AsitList::BOOLEAN )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest23( int $case, mixed $list ) : void
    {
        $case += 230;
        $ok   = 0;
        try {
            $list->setValueType( ItList::INT )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
        $ok = 0;
        try {
            $list->setValueType( ItList::INTEGER )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest24( int $case, mixed $list ) : void
    {
        $case += 240;
        $ok   = 0;
        try {
            $list->setValueType( ItList::DOUBLE )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
        $ok = 0;
        try {
            $list->setValueType( ItList::FLOAT )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest25( int $case, mixed $list ) : void
    {
        $case += 250;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::STRING )
                 ->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest26( int $case, mixed $list ) : void
    {
        $case += 260;
        $ok   = 0;
        try {
            $list->setValueType( ItList::OBJECT )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest27( int $case, mixed $list ) : void
    {
        $case += 270;
        $ok   = 0;
        try {
            $list->setValueType( ItList::RESOURCE )
                 ->assertElementType( ItList::STRING );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest28( int $case, mixed $list ) : void
    {
        $case += 280;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::CALL_BLE )
                 ->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType Traversable
     *
     * @test
     */
    public function listTest29() : void
    {
        $asmittagList = AsmittagList::factory(Traversable::class );

        $ok   = 0;
        try {
            $asmittagList->assertElementType( new TestClass3());
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertEquals(
            1, $ok, __FUNCTION__ . ' #1 exp 1, got ' . $ok
        );

        $ok   = 0;
        try {
            $asmittagList->append( new TestClass3());
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertEquals(
            1, $ok, __FUNCTION__ . ' #2 exp 1, got ' . $ok
        );

        $ok   = 0;
        try {
            $asmittagList->append( new stdClass());
            $ok = 1;
        }
        catch( TypeException $e ) {
            $ok = 2;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #3 exp 2, got ' . $ok
        );
    }

    /**
     * test assertElementType
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest30( int $case, mixed $list ) : void
    {
        $case += 300;
        $ok   = 0;
        try {
            $list->setValueType( DateTimeInterface::class )
                ->assertElementType( new DateTime() );
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertEquals(
            1, $ok, __FUNCTION__ . ' #1-' . $case . ', exp 1, got ' . $ok
        );

        $ok = 0;
        try {
            $list->setValueType( TestIfc::class )
                 ->assertElementType( new testClass2());
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #2-' . $case . ', exp 2, got ' . $ok
        );

        $ok = 0;
        try {
            $list->setValueType( AsitList::class )
                 ->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #3-' . $case . ', exp 2, got ' . $ok
        );

        $ok = 0;
        try {
            $list->setValueType( AsittagList::class )
                 ->assertElementType( new DateTime() );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #4-' . $case . ', exp 2, got ' . $ok
        );

        $ok = 0;
        try {
            $list->setValueType( AsittagList::class )
                 ->assertElementType( fopen( self::$file1, 'rb' ));
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertEquals(
            2, $ok, __FUNCTION__ . ' #5-' . $case . ', exp 2, got ' . $ok
        );
    }

    /**
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest400( int $case, mixed $list ) : void
    {
        $case += 400;
        foreach(
            [
                AsitList::BOOL,
                AsitList::BOOLEAN,
                AsitList::INT,
                AsitList::INTEGER,
                AsitList::FLOAT,
                AsitList::ARR_Y,
                AsitList::ARRAY2,
                AsitList::DOUBLE,
                AsitList::STRING,
                AsitList::OBJECT,
                AsitList::RESOURCE,
                AsitList::CALL_BLE,
                AsitList::TRAVERSABLE,
            ]
            as $vIx => $valueType ) {
            $list->setValueType( $valueType );
            $this->assertEquals(
                (( AsitList::ARRAY2 === $valueType ) ? AsitList::ARR_Y : $valueType ),
                $list->getValueType(),
                __FUNCTION__ . ' #' . $case . '-1-' . $vIx . ', exp : ' . $valueType . ', got : ' . $list->getValueType()
            );

            $ok = 0;
            try {
                $list->init();
                $list->setCollection( $this->arrayListLoader( $valueType ) );
                $ok = 1;
            }
            catch( Exception $e ) {
                $ok = $e->getMessage();
            }
            $this->assertEquals(
                1, $ok, __FUNCTION__ . ' #' . $case . '-2-' . $vIx . ' - ' . get_class( $list ) . ' - ' . $valueType . ', exp 1, got ' . $ok
            );

        } // end foreach
    }

    /**
     * test fqcn
     *
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest500( int $case, mixed $list ) : void
    {
        $case += 500;
        $list->setValueType( DateTime::class );

        for( $ix = 0; $ix < 3; $ix++ ) {
            $list->append( new DateTime() );
        }

        foreach( $list as $element ) {
            $this->asserttrue(
                ( $element instanceof DateTime ),
                __FUNCTION__ . ' #1-' . $case . '-' . $ix
            );
        }
    }

    /**
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function listTest600( int $case, mixed $list ) : void
    {
        $case  += 600;
        $string = '';
        $class  = get_class( $list );

        foreach(
            [
                AsitList::BOOL,
                AsitList::BOOLEAN,
                AsitList::INT,
                AsitList::INTEGER,
                AsitList::FLOAT,
                AsitList::ARR_Y,
                AsitList::ARRAY2,
                AsitList::DOUBLE,
                AsitList::STRING,
                AsitList::OBJECT,
                AsitList::RESOURCE,
                AsitList::CALL_BLE,
            ]
            as $vIx => $valueType ) {
            $list->setValueType( $valueType );
            $list->init();
            for( $x = 0; $x < 99; $x++  ) {
                foreach( $this->arrayListLoader( $valueType ) as $pKey => $element ) {
                    $list->append( $element, $x . $pKey );
                    if( $class === ItList::class ) {
                        continue;
                    }
                    if(( in_array( $class, [ AsmitList::class, AsmittagList::class ], true ) ) &&
                        ( 0 === ( $list->key() % 3 ))) {
                        $list->addCurrentPkey( 'test' . $x );
                    }
                    if( in_array( $class, [ AsittagList::class, AsmittagList::class ], true ) ) {
                        $list->addCurrentTag( self::getAttribute( $list->key()));
                        $list->addCurrentTag( self::getAttribute(( $list->key() + 5 )));
                    }
                } // end foreach
            } // end for
            $string = $list->toString();
            $list->seek( array_rand( array_flip( range( 2, 192 )))); // set current
            $this->assertNotFalse(
                strpos( $string, (string)$list->key() ), __FUNCTION__ . ' #' . $case . '-' . 1 . ' key:' . $list->key() . ' ' . get_class( $list ) . PHP_EOL . $string
            );
        } // end foreach
        /*
        if( $class == AsmittagList::class ) {
            echo $class . ' : ' . PHP_EOL . $string . PHP_EOL; // the very last
        }
        */
    }

    /**
     * Test AsmitList and remove
     *
     * Same as Asit2Test::asitTest76() but on AsmitList
     *
     * @test
     */
    public function listTest700() : void
    {
        $asit = AsmitList::factory( AsmitList::STRING )
            ->append( 'value1', 'key1' )
            ->append( 'value2', 'key2' )
            ->append( 'value3', 'key3' )
            ->append( 'value4', 'key4' )
            ->append( 'value5', 'key5' );
        $asit->last();
        $asit->previous();
        $asit->previous();
        $key = $asit->key();
        $this->assertEquals( 2, $key, __FUNCTION__ . '-1, exp 2, got: ' . $key );

        $pKey = $asit->getCurrentPkey();

        $asit->remove();
        // check that pkey NOT exists
        $this->assertEmpty(
            $asit->pKeyGet( $pKey ),
            __FUNCTION__ . '-2, pkey: ' . $pKey . ', exp none, got ' . var_export( $asit->pKeyGet( $pKey ), true )
        );
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

    /**
     * Test AsmittagList and remove, last and previous
     *
     * Same as Asit2Test::asitTest76() but on AsmitList
     *
     * @test
     *
     */
    public function listTest800() : void
    {
        $asit = AsmittagList::factory( AsmitList::STRING )
            ->append( 'value1', 'key1', 'tag1' )
            ->append( 'value2', 'key2', 'tag2' )
            ->append( 'value3', 'key3', 'tag3' )
            ->append( 'value4', 'key4', 'tag4' )
            ->append( 'value5', 'key5', 'tag5' );
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

    /**
     * @param mixed $valueType
     * @return mixed
     */
    public static function getElement( mixed $valueType ) : mixed
    {
        $ix = array_rand( [ 0, 1 ] );
        switch( $valueType ) {
            case AsitList::ARR_Y :
            case AsitList::ARRAY2 :
                static $arrays = [
                    [ 1, 2 ],
                    [ 'one', 'two' ]
                ];
                return $arrays[$ix];
            case AsitList::BOOL :
            case AsitList::BOOLEAN :
                static $bools = [ true, false ];
                return $bools[$ix];
            case AsitList::INT :
            case AsitList::INTEGER :
                static $ints = [ 1, 2 ];
                return $ints[$ix];
            case AsitList::DOUBLE :
            case AsitList::FLOAT :
                static $floats = [ 1.1, 2.2 ];
                return $floats[$ix];
            case AsitList::STRING :
                static $strings = [ 'test1', 'test2' ];
                return $strings[$ix];
            case AsitList::OBJECT :
                $objects =  [
                    new DateTime(),
                    new testClass2()
                ];
                return $objects[$ix];
            case AsitList::RESOURCE :
                static $files = [];
                if( empty( $files )) {
                    $files = [
                        fopen( self::$file1, 'rb' ),
                        fopen( self::$file2, 'rb' )
                    ];
                }
                return $files[$ix];
            case AsitList::CALL_BLE :
                static $callables = [];
                if( empty( $callables )) {
                    $callables = [
                        new testClass2(),
                        new testClass2()
                    ];
                }
                return $callables[$ix];
            case AsitList::TRAVERSABLE :
                return It::factory( [ 1, 2 ] );
            default :
                $fqcns = [ DateTime::class, testClass2::class ];
                return $fqcns[$ix];
        } // end switch

    }

    /**
     * @param mixed $valueType
     * @return array
     */
    public function arrayListLoader( mixed $valueType ) : array
    {

        $output = [];
        for( $ix=0; $ix < 2; $ix++ ) {
            $output['key' . $ix] = self::getElement( $valueType );
        } // end for

        return $output;
    }
}
