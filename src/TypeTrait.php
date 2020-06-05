<?php
/**
 * Asit package manages array collections
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

use InvalidArgumentException;

use function class_exists;
use function get_class;
use function gettype;
use function in_array;
use function interface_exists;
use function is_array;
use function is_bool;
use function is_callable;
use function is_int;
use function is_float;
use function is_resource;
use function is_string;
use function sprintf;
use function var_export;

trait TypeTrait
{

    /**
     * @var string
     */
    protected $valueType = null;

    /**
     * Assert collection element value type, extended 'gettype'
     *
     * @param mixed $element
     */
    public function assertElementType( $element ) {
        static $ERR = 'Invalid value type (#%d) : %s, expects %s';
        if( ! $this->isValueTypeSet()) {
            return;
        }
        $errType = 0;
        switch( $this->valueType ) {
            case self::ARR_Y :
            case self::ARRAY2 :
            if( ! is_array( $element )) {
                $errType = 1;
            }
            break;
            case self::BOOL :
            case self::BOOLEAN :
                if( ! is_bool( $element )) {
                    $errType = 2;
                }
                break;
            case self::INT :
            case self::INTEGER :
                if( ! is_int( $element )) {
                    $errType = 3;
                }
                break;
            case self::DOUBLE :
            case self::FLOAT :
                if( ! is_float( $element )) {
                    $errType = 4;
                }
                break;
            case self::STRING :
                if( ! is_string( $element )) {
                    $errType = 5;
                }
                break;
            case self::OBJECT :
                if( self::OBJECT != gettype( $element )) {
                    $errType = 6;
                }
                break;
            case self::RESOURCE :
                if( ! is_resource( $element )) {
                    $errType = 7;
                }
                break;
            case self::CALL_BLE :
                if( ! is_callable( $element, true )) {
                    $errType = 8;
                }
                break;
            default :
                if(( self::OBJECT != gettype( $element )) ||
                    ! ( $element instanceof $this->valueType )) {
                    $errType = 9;
                    break;
                }
                break;
        } // end switch
        if( ! empty( $errType )) {
            $getType = gettype( $element );
            switch( true ) {
                case ( self::OBJECT == $getType ) :
                    $type = get_class( $element );
                    break;
                case ( self::RESOURCE == $getType ) :
                    $type = self::RESOURCE;
                    break;
                default :
                    $type = $getType;
                    break;
            } // end switch
            throw new InvalidArgumentException( sprintf( $ERR, $errType, $type, $this->valueType ));
        } // end if
    }

    /**
     * @return string
     */
    public function getValueType() {
        return $this->valueType;
    }

    /**
     * @return bool
     */
    public function isValueTypeSet() {
        return ( null !== $this->valueType );
    }

    /**
     * @param string $valueType
     * @return static
     * @throws InvalidArgumentException
     */
    public function setValueType( $valueType ) {
        if( $valueType == self::ARRAY2 ) {
            $valueType = self::ARR_Y;
        }
        self::assertValueType( $valueType );
        $this->valueType = $valueType;
        return $this;
    }

    /**
     * Assert value type, extended 'gettype'
     *
     * Accept TypeInterface constants or FQCN (for class or interface)
     *
     * @param string $valueType
     * @throws InvalidArgumentException
     */
    public static function assertValueType( $valueType ) {
        static $TYPES = [
            self::BOOL,
            self::BOOLEAN,
            self::INT,
            self::INTEGER,
            self::FLOAT,
            self::ARR_Y,
            self::DOUBLE,
            self::STRING,
            self::OBJECT,
            self::RESOURCE,
            self::CALL_BLE,
        ];
        static $ERR = 'Invalid value type : %s';
        if( $valueType == self::ARRAY2 ) {
            return;
        }
        if( in_array( $valueType, $TYPES )) {
            return;
        }
        if( is_string( $valueType ) &&
            ( class_exists( $valueType ) || interface_exists( $valueType ))) {
            return;
        }
        throw new InvalidArgumentException( sprintf( $ERR, var_export( $valueType, true )));
    }

}
