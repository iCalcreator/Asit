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
 * Class ItList extends It, assure collection elements of expected valueType
 *
 * @package Kigkonsult\Asit
 */
class ItList
    extends It
    implements TypeInterface
{

    use TypeTrait;

    use ListTrait;

    /**
     * Append assured element to (array) collection
     *
     * @override
     * @param mixed $element
     * @return static
     * @throws InvalidArgumentException
     */
    public function append( $element ) {
        if( $this->isValueTypeSet()) {
            $this->assertElementType( $element );
        }
        return parent::append( $element );
    }

}
