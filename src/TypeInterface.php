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

/**
 * Interface TypeInterface
 *
 * Type constants
 *
 * @package Kigkonsult\Asit
 */
interface TypeInterface
{

    const ARR_Y    = 'array';
    const ARRAY2   = '[]';
    const BOOL     = 'bool';
    const BOOLEAN  = 'boolean';
    const DOUBLE   = 'double';
    const INT      = 'int';
    const INTEGER  = 'integer';
    const FLOAT    = 'float';
    const STRING   = 'string';
    const OBJECT   = 'object';
    const RESOURCE = 'resource';
    const CALL_BLE = 'callable';

}
