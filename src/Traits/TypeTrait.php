<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
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
namespace Kigkonsult\Asit\Traits;

use Kigkonsult\Asit\Exceptions\TypeException;
use Traversable;

use function class_exists;
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

/**
 * Trait TypeTrait, property and methods for collection element value types
 *
 * @package Kigkonsult\Asit
 */
trait TypeTrait
{
    /**
     * The collection element value type
     *
     * @var string|null
     */
    protected ? string $valueType = null;

    /**
     * Assert collection element value type, extended 'gettype'
     *
     * @param mixed $element
     * @return void
     * @throws TypeException
     */
    public function assertElementType( mixed $element ) : void
    {
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
                if( ! is_object( $element ) ) {
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
            case self::TRAVERSABLE :
                if( ! $element instanceof Traversable ) {
                    $errType = 8;
                }
                break;
            default :
                if( ! is_object( $element ) ||
                    ! ( $element instanceof $this->valueType )) {
                    $errType = 9;
                    break;
                }
                break;
        } // end switch
        if( ! empty( $errType )) {
            throw new TypeException(
                sprintf(
                    TypeException::$ERR1,
                    $errType,
                    self::getErrType( $element ),
                    $this->valueType
                )
            );
        } // end if
    }

    /**
     * Return the collection element value type
     *
     * @return null|string
     */
    public function getValueType() : null | string
    {
        return $this->valueType;
    }

    /**
     * Return bool true if collection is not empty
     *
     * @return bool
     */
    public function isValueTypeSet() : bool
    {
        return ( null !== $this->valueType );
    }

    /**
     * Set collection element value type
     *
     * @param string $valueType
     * @return static
     * @throws TypeException
     */
    public function setValueType( string $valueType ) : static
    {
        if( $valueType === self::ARRAY2 ) {
            $valueType = self::ARR_Y;
        }
        self::assertValueType( $valueType );
        $this->valueType = $valueType;
        return $this;
    }

    /**
     * Assert value type, extended 'gettype'
     *
     * Accept ListTypeInterface constants or FQCN (for class or interface)
     *
     * @param string $valueType
     * @return void
     * @throws TypeException
     */
    public static function assertValueType( string $valueType ) : void
    {
        static $STDTYPES = [
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
            self::TRAVERSABLE,
        ];
        switch( true ) {
            case ( $valueType === self::ARRAY2 ) : // fall through
            case in_array( $valueType, $STDTYPES, true ) : // fall through
            case ( class_exists( $valueType ) || interface_exists( $valueType )) :
                return;
            default :
                throw new TypeException(
                    sprintf( TypeException::$ERR2, self::getDispVal( $valueType ))
                );
        } // end switch
    }
}
