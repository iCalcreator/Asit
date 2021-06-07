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
declare( strict_types = 1 );
namespace Kigkonsult\Asit;

/**
 * Interface ListTypeInterface
 *
 * Type constants
 *
 * @package Kigkonsult\Asit
 */
interface ListTypeInterface
{
    /**
     * Asit constants, OBJECT && RESOURCE in class It
     */
    const ARR_Y       = "array";
    const ARRAY2      = "[]";
    const BOOL        = "bool";
    const BOOLEAN     = "boolean";
    const CALL_BLE    = "callable";
    const DOUBLE      = "double";
    const INT         = "int";
    const INTEGER     = "integer";
    const FLOAT       = "float";
    const STRING      = "string";
    const TRAVERSABLE = "Traversable";
}
