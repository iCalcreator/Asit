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

use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

interface testIfc
{
    const HALLO_WORLD = 'Hallo world';
}
class testClass1
    implements testIfc
{
    public function __invoke() {
        return self::HALLO_WORLD;
    }
}

class testClass2
{
    public function __invoke() {
        return 'Hallo world';
    }
}


class AsitListTest extends TestCase
{
    public static $file1 = 'file1';
    public static $file2 = 'file1';

    public static function setUpBeforeClass() {
        touch( self::$file1 );
        touch( self::$file2 );
    }

    public static function tearDownAfterClass() {
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
    public function ListTest0() {
        $data1  = [ 'key1' => 'value1' ];
        $data24 = [ 'key2' => 'value2', 'key3' => 'value3', 'key4' => 'value4', ];
        $list1 = new AsmitList( $data1, AsmitList::STRING);
        foreach( $data24 as $key => $value ) {
            $list1->append( $value, $key );
        }
        $list2 =             AsmitList::factory( $data1, AsmitList::STRING)
            ->append( 'value2', 'key2' )
            ->append( 'value3', 'key3' )
            ->append( 'value4', 'key4' );

        $this->assertEquals(
            $list1->get(),
            $list2->get(),
            'test0'
        );
        $this->assertEquals(
            $list1->getPkeyIterator(),
            $list2->getPkeyIterator(),
            'test1'
        );
    }

    /**
     * dataProvider
     *
     * @return array
     */
    public function listLoader() {
        $testData = [];

        $testData[] = [
            1,
            new ItList()
        ];

        $testData[] = [
            2,
            new AsitList()
        ];

        $testData[] = [
            3,
            new AsmitList()
        ];

        $testData[] = [
            4,
            AsittagList::factory()
        ];

        $testData[] = [
            5,
            AsmittagList::factory()
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
    public function ListTest1( $case, $list ) {
        $case += 10;
        $ok    = 0;
        try {
            $list->assertValueType( AsitList::ARRAY2 );
            $list->assertValueType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test1-1-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest20( $case, $list ) {
        $case += 200;
        $ok    = 0;
        try {
            $list->assertElementType( 'string' );
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertTrue( $ok == 1, 'test20-' . $case . ', exp 1, got ' . $ok );
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
    public function ListTest21( $case, $list ) {
        $case += 210;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::ARR_Y )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test21-1-' . $case . ', exp 2, got ' . $ok );
        $ok = 0;
        try {
            $list->setValueType( AsitList::ARRAY2 )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test21-2-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest22( $case, $list ) {
        $case += 220;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::BOOL )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test22-1-' . $case . ', exp 2, got ' . $ok );
        $ok = 0;
        try {
            $list->setValueType( AsitList::BOOLEAN )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test22-2-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest23( $case, $list ) {
        $case += 230;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::INT )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test23-1-' . $case . ', exp 2, got ' . $ok );
        $ok = 0;
        try {
            $list->setValueType( AsitList::INTEGER )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test23-2-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest24( $case, $list ) {
        $case += 240;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::DOUBLE )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test24-1-' . $case . ', exp 2, got ' . $ok );
        $ok = 0;
        try {
            $list->setValueType( AsitList::FLOAT )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test24-2-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest25( $case, $list ) {
        $case += 250;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::STRING )->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test25-1-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest26( $case, $list ) {
        $case += 260;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::OBJECT )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test26-1-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest27( $case, $list ) {
        $case += 270;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::RESOURCE )->assertElementType( 'string' );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test27-1-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest28( $case, $list ) {
        $case += 280;
        $ok   = 0;
        try {
            $list->setValueType( AsitList::CALL_BLE )->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test28-1-' . $case . ', exp 2, got ' . $ok );
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
    public function ListTest29( $case, $list ) {
        $case += 290;
        $ok   = 0;
        try {
            $list->setValueType( testIfc::class )->assertElementType( new testClass1() );
            $ok = 1;
        }
        catch( Exception $e ) {
            $ok = 2;
        }
        $this->assertTrue( $ok == 1, 'test29-1-' . $case . ', exp 1, got ' . $ok );
        $ok = 0;
        try {
            $list->setValueType( testIfc::class )->assertElementType( new testClass2() );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test29-2-' . $case . ', exp 2, got ' . $ok );

        $ok = 0;
        try {
            $list->setValueType( AsitList::class )->assertElementType( 12345 );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test29-3-' . $case . ', exp 2, got ' . $ok );

        $ok = 0;
        try {
            $list->setValueType( AsittagList::class )->assertElementType( new testClass1() );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test29-4-' . $case . ', exp 2, got ' . $ok );

        $ok = 0;
        try {
            $list->setValueType( AsittagList::class )->assertElementType( fopen( self::$file1, 'r' ) );
            $ok = 1;
        }
        catch( InvalidArgumentException $e ) {
            $ok = 2;
        }
        catch( Exception $e ) {
            $ok = 3;
        }
        $this->assertTrue( $ok == 2, 'test29-5-' . $case . ', exp 2, got ' . $ok );
    }

    /**
     * @test
     * @dataProvider listLoader
     *
     * @param int $case
     * @param mixed $list
     */
    public function ListTest3( $case, $list ) {
        $case += 300;
        foreach(
            [
                AsitList::BOOL,
                AsitList::BOOLEAN,
                AsitList::INT,
                AsitList::INTEGER,
                AsitList::FLOAT,
                AsitList::ARR_Y,
                AsitList::DOUBLE,
                AsitList::STRING,
                AsitList::OBJECT,
                AsitList::RESOURCE,
                AsitList::CALL_BLE,
            ]
            as $vIx => $valueType ) {
            $list->setValueType( $valueType );
            $this->assertEquals(
                $valueType,
                $list->getValueType(),
                'test' . $case . '-1-' . $vIx . ', exp : ' . $valueType . ', got : ' . $list->getValueType()
            );

            $ok = 0;
            try {
                $list->init();
                $list->setCollection( $this->arrayLoader( $valueType ) );
                $ok = 1;
            }
            catch( Exception $e ) {
                $ok = $e->getMessage();
            }
            $this->assertTrue(
                ( $ok == 1 ),
                'test' . $case . '-2-' . $vIx . ' - ' . get_class( $list ) . ' - ' . $valueType . ', exp 1, got ' . $ok
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
    public function ListTest4( $case, $list ) {
        $case += 400;
        $list->setValueType( testClass1::class );

        for( $ix = 0; $ix < 3; $ix++ ) {
            $list->append( new testClass1() );
        }

        foreach( $list as $element ) {
            $this->asserttrue(
                ( $element instanceof testClass1 ),
                'test4-1-' . $case . '-' . $ix
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
    public function ListTest5( $case, $list ) {
        $case  += 500;
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
                foreach( $this->arrayLoader( $valueType ) as $pKey => $element ) {
                    $list->append( $element, $x . $pKey );
                    if( $class == ItList::class ) {
                        continue;
                    }
                    if(( in_array( $class, [ AsmitList::class, AsmittagList::class ] )) &&
                        ( 0 == ( $list->key() % 3 ))) {
                        $list->addCurrentPkey( 'test' . $x );
                    }
                    if( in_array( $class, [ AsittagList::class, AsmittagList::class ] )) {
                        $list->addCurrentTag( self::getAttribute( $list->key()));
                        $list->addCurrentTag( self::getAttribute(( $list->key() + 5 )));
                    }
                } // end foreach
            } // end for
            $string = $list->toString();
            $list->seek( array_rand( array_flip( range( 2, 192 )))); // set current
            $this->assertTrue(
                ( false !== strpos( $string, (string) $list->key())),
                'test' . $case . '-' . 1 . ' key:' . $list->key() . ' ' .  get_class( $list ) . PHP_EOL . $string
            );
        } // end foreach
        /*
        if( $class == AsmittagList::class ) {
            echo $class . ' : ' . PHP_EOL . $string . PHP_EOL; // the very last
        }
        */
    }

    public static function getElement( $valueType ) {
        $ix = array_rand( [ 0, 1 ] );
        switch( $valueType ) {
            case AsitList::ARR_Y :
            case AsitList::ARRAY2 :
                static $arrays = [
                    [ 1, 2 ],
                    [ 'one', 'two' ]
                ];
                return $arrays[$ix];
                break;
            case AsitList::BOOL :
            case AsitList::BOOLEAN :
                static $bools = [ true, false ];
                return $bools[$ix];
                break;
            case AsitList::INT :
            case AsitList::INTEGER :
                static $ints = [ 1, 2 ];
                return $ints[$ix];
                break;
            case AsitList::DOUBLE :
            case AsitList::FLOAT :
                static $floats = [ 1.1, 2.2 ];
                return $floats[$ix];
                break;
            case AsitList::STRING :
                static $strings = [ 'test1', 'test2' ];
                return $strings[$ix];
                break;
            case AsitList::OBJECT :
                $objects =  [
                    new testClass1(),
                    new testClass2()
                ];
                return $objects[$ix];
                break;
            case AsitList::RESOURCE :
                static $files = [];
                if( empty( $files )) {
                    $files = [
                        fopen( self::$file1, 'r' ),
                        fopen( self::$file2, 'r' )
                    ];
                }
                return $files[$ix];
                break;
            case AsitList::CALL_BLE :
                static $callables = [];
                if( empty( $callables )) {
                    $callables = [
                        new testClass1(),
                        new testClass2()
                    ];
                }
                return $callables[$ix];
                break;
            default :
                $fqcns = [ testClass1::class, testClass2::class ];
                return $fqcns[$ix];
                break;
        } // end switch

    }
    public function arrayLoader( $valueType ) {

        $output = [];
        for( $ix=0; $ix < 2; $ix++ ) {
            $output['key' . $ix] = self::getElement( $valueType );
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

    public static function getAttribute( $index ) {
        $cIx = $index % 10;
        return self::$COLORS[$cIx];
    }

}
