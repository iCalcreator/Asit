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

use Kigkonsult\Asit\Exceptions\CollectionException;
use Kigkonsult\Asit\Exceptions\TypeException;

use function is_string;

/**
 * Trait ListTrait, pKey/tag methods
 *
 * @package Kigkonsult\Asit
 */
trait ListTrait
{
    /**
     * Extended constructor, accepts value type as single or second argument
     *
     * @override
     * @param mixed|null $collection
     * @param string|null $valueType
     * @throws CollectionException
     * @throws TypeException
     */
    public function __construct( mixed $collection = null, ? string $valueType = null )
    {
        switch( true ) {
            case ( is_string( $collection ) && ( null === $valueType )) :
                $this->setValueType( $collection );
                $collection = null;
                break;
            case ( null !== $valueType ) :
                $this->setValueType( $valueType );
                break;
        } // end switch
        parent::__construct( $collection );
    }

    /**
     * Extended factory method, accepts value type as single or second argument
     *
     * @override
     * @param mixed|null $collection
     * @param mixed|null $dummy
     * @return static
     */
    public static function factory( mixed $collection = null, mixed $dummy = null ) : static
    {
        return new static( $collection, $dummy );
    }

    /**
     * Extended class singleton method
     *
     * @override
     * @param mixed|null $collection
     * @param mixed|null $dummy
     * @return static
     * @throws CollectionException
     * @throws TypeException
     */
    public static function singleton( mixed $collection = null, mixed $dummy = null ) : static
    {
        static $instance = null;
        if( null === $instance ) {
            $instance = new static( $collection, $dummy );
        }
        return $instance;
    }
}
