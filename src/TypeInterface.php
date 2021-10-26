<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * Support <https://github.com/iCalcreator/Asit>
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-21 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
 * @link      https://kigkonsult.se
 * @version   2.0
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
}
