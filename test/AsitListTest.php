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
     * test assertValueType
     *
     * @test
     */
    public function ListTest1() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->assertValueType( AsitList::ARRAY2 );
                $list->assertValueType( 12345 );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test1-1-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest20() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->assertElementType( 'string' );
                $ok = 1;
            } catch( Exception $e ) {
                $ok = 2;
            }
            $this->assertTrue( $ok == 1, 'test20-' . $lIx . ', exp 1, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest21() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::ARR_Y )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test21-1-' . $lIx . ', exp 2, got ' . $ok );
            $ok = 0;
            try {
                $list->setValueType( AsitList::ARRAY2 )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test21-2-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest22() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::BOOL )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test22-1-' . $lIx . ', exp 2, got ' . $ok );
            $ok = 0;
            try {
                $list->setValueType( AsitList::BOOLEAN )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test22-2-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest23() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::INT )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test23-1-' . $lIx . ', exp 2, got ' . $ok );
            $ok = 0;
            try {
                $list->setValueType( AsitList::INTEGER )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test23-2-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest24() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::DOUBLE )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test24-1-' . $lIx . ', exp 2, got ' . $ok );
            $ok = 0;
            try {
                $list->setValueType( AsitList::FLOAT )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test24-2-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest25() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::STRING )->assertElementType( 12345 );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test25-1-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest26() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::OBJECT )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test26-1-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest27() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::RESOURCE )->assertElementType( 'string' );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test27-1-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest28() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( AsitList::CALL_BLE )->assertElementType( 12345 );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test28-1-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * test assertElementType
     *
     * @test
     */
    public function ListTest29() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $lIx => $list ) {
            $ok = 0;
            try {
                $list->setValueType( testIfc::class )->assertElementType( new testClass1() );
                $ok = 1;
            } catch( Exception $e ) {
                $ok = 2;
            }
            $this->assertTrue( $ok == 1, 'test29-1-' . $lIx . ', exp 1, got ' . $ok );
            $ok = 0;
            try {
                $list->setValueType( testIfc::class )->assertElementType( new testClass2() );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test29-2-' . $lIx . ', exp 2, got ' . $ok );

            $ok = 0;
            try {
                $list->setValueType( AsitList::class )->assertElementType( 12345 );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test29-3-' . $lIx . ', exp 2, got ' . $ok );

            $ok = 0;
            try {
                $list->setValueType( AsittagList::class )->assertElementType( new testClass1() );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test29-4-' . $lIx . ', exp 2, got ' . $ok );

            $ok = 0;
            try {
                $list->setValueType( AsittagList::class )->assertElementType( fopen( self::$file1, 'r' ) );
                $ok = 1;
            } catch( InvalidArgumentException $e ) {
                $ok = 2;
            } catch( Exception $e ) {
                $ok = 3;
            }
            $this->assertTrue( $ok == 2, 'test29-5-' . $lIx . ', exp 2, got ' . $ok );
        } // end foreach
    }

    /**
     * @test
     */
    public function ListTest3() {
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

            foreach( [
                new ItList( null, $valueType ),
                new AsitList( null, $valueType ),
                AsittagList::factory( [], $valueType ),
            ] as $cIx => $list ) {

                $this->assertEquals(
                    $valueType,
                    $list->getValueType(),
                    'test3-1, exp : ' . $valueType . ', got : ' . $list->getValueType()
                );

                $ok = 0;
                try {
                    $list->setCollection( $this->arrayLoader( $valueType ));
                    $ok = 1;
                }
                catch( Exception $e ) {
                    $ok = $e->getMessage();
                }
                $this->assertTrue(
                    ( $ok == 1 ),
                    'test3-2 - ' . get_class( $list ) . ' - ' . $valueType . ', exp 1, got ' . $ok
                );

            } // end foreach 2
        } // end foreach 1

    }

    /**
     * test fqcn
     *
     * @test
     */
    public function ListTest4() {
        foreach( [ new ItList(), new AsitList(), AsittagList::factory(), ] as $cIx => $list ) {
            $list->setValueType( testClass1::class );

            for( $ix = 0; $ix < 3; $ix++ ) {
                $list->append( new testClass1() );
            }

            foreach( $list as $element ) {
                $this->asserttrue(
                    ( $element instanceof testClass1 ),
                    'test4-1'
                );
            }
        } // end foreach

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

}
