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

use Kigkonsult\Asit\Exceptions\TypeException;

/**
 * Interface ListTypeInterface
 *
 * The type constants and list 'contract' methods
 *
 * @package Kigkonsult\Asit
 * @since 2.3.07 2024-03-12
 */
interface ListTypeInterface
{
    /**
     * Asit constants, OBJECT && RESOURCE in class It
     */
    public const ARR_Y       = "array";
    public const ARRAY2      = "[]";
    public const BOOL        = "bool";
    public const BOOLEAN     = "boolean";
    public const CALL_BLE    = "callable";
    public const DOUBLE      = "double";
    public const INT         = "int";
    public const INTEGER     = "integer";
    public const FLOAT       = "float";
    public const STRING      = "string";
    public const TRAVERSABLE = "Traversable";

    /**
     * Extended It constructor
     *
     * Value type (required) as single or second argument
     *
     * @override It::__construct()
     * @param mixed|null $collection
     * @param string|null $valueType
     * @throws TypeException
     */
    public function __construct( mixed $collection = null, ? string $valueType = null );

    /**
     * Extended It factory method
     *
     * Value type (required) as single or second argument
     *
     * @override It::factory()
     * @param mixed|null $collection
     * @param mixed $valueType
     * @return static
     * @throws TypeException
     */
    public static function factory( mixed $collection = null, mixed $valueType = null ) : static;


    /**
     * Extended It singleton method on list-type and valueType
     *
     * Value type (required) as single or second argument
     *
     * @override it::singleton()
     * @param mixed|null $collection
     * @param mixed|null $valueType
     * @return static
     * @throws TypeException
     */
    public static function singleton( mixed $collection = null, mixed $valueType = null ) : static;


    /**
     * Instance method alias, return singleton instance on list-type and valueType
     *
     * Value type (required) as single or second argument
     *
     * @param mixed|null $collection
     * @param mixed|null $valueType
     * @return static
     */
    public static function getInstance( mixed $collection = null, mixed $valueType = null ) : static;


    /**
     * Class clone method
     *
     * @return void
     */
    public function __clone() : void;


    /**
     * Assert value type, extended 'gettype'
     *
     * Accept ListTypeInterface constants or FQCN (for class or interface)
     *
     * @param null|string $valueType
     * @return void
     * @throws TypeException
     */
    public static function assertValueType( ? string $valueType ) : void;

    /**
     * Assert collection element value type, extended 'gettype'
     *
     * @param mixed $element
     * @return void
     * @throws TypeException
     */
    public function assertElementType( mixed $element ) : void;

    /**
     * Return the collection element value type
     *
     * @return null|string
     */
    public function getValueType() : null|string;

    /**
     * Set collection element value type
     *
     * @param string $valueType
     * @return static
     * @throws TypeException
     */
    public function setValueType( string $valueType ) : static;
}
