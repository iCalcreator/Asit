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

/**
 * Trait ListTrait, pKey/tag methods
 *
 * @package Kigkonsult\Asit
 */
trait ListTrait
{

    /**
     * Extended construct method
     *
     * @override
     * @param  array $collection
     * @param  string $valueType
     * @throws InvalidArgumentException
     */
    public function __construct( $collection = [], $valueType = null ) {
        if( ! empty( $valueType )) {
            $this->setValueType( $valueType );
        }
        parent::__construct( $collection );
    }

    /**
     * Extended factory method
     *
     * @override
     * @param  array $collection
     * @param  string $valueType
     * @return static
     * @throws InvalidArgumentException
     */
    public static function factory( $collection = [], $valueType = null ) {
        return new static( $collection, $valueType );
    }

}
