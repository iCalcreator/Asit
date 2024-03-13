<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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

use function is_object;
use function is_string;

/**
 * Trait ListTrait, pKey/tag methods
 *
 * @package Kigkonsult\Asit
 */
trait ListTrait
{
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
    public function __construct( mixed $collection = null, ? string $valueType = null )
    {
        parent::__construct();
        [ $collection, $valueType ] = self::getCollectionValueType( $collection, $valueType );
        self::assertValueType( $valueType );
        $this->setValueType((string) $valueType );
        if( null !== $collection ) {
            $this->setCollection( $collection );
        }
    }

    /**
     * @param mixed|null  $collection
     * @param string|null $valueType
     * @return array|null[]
     */
    protected static function getCollectionValueType( mixed $collection = null, ? string $valueType = null ) : array
    {
        return ( is_string( $collection ) && ( null === $valueType ))
            ? [ null, $collection ]
            : [ $collection, $valueType ];
    }

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
    public static function factory( mixed $collection = null, mixed $valueType = null ) : static
    {
        return new static( $collection, $valueType );
    }

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
    public static function singleton( mixed $collection = null, mixed $valueType = null ) : static
    {
        $cx  = static::class;
        [ $collection, $valueType ] = self::getCollectionValueType( $collection, $valueType );
        self::assertValueType( $valueType );
        $cx .= $valueType;
        if( ! isset( self::$instances[$cx] )) {
            self::$instances[$cx] = new static( $collection, $valueType );
        }
        return self::$instances[$cx];
    }

    /**
     * Instance method alias, return singleton instance on list-type and valueType
     *
     * Value type (required) as single or second argument
     *
     * @param mixed|null $collection
     * @param mixed|null $valueType
     * @return static
     */
    public static function getInstance( mixed $collection = null, mixed $valueType = null ) : static
    {
        return static::singleton( $collection, $valueType );
    }

    /**
     * Class clone method
     *
     * @return void
     */
    public function __clone() : void
    {
        if(( self::OBJECT !== $this->valueType ) && self::isStdType( $this->valueType )) {
            return;
        }
        for( $this->rewind(); $this->valid(); $this->next()) {
            $ix = $this->key();
            if( is_object( $this->collection[$ix] )) {
                $this->collection[$ix] = clone $this->collection[$ix];
            }
        }
    }
}
