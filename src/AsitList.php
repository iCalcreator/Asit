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

use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\TypeException;
use Kigkonsult\Asit\Traits\TypeTrait;
use Kigkonsult\Asit\Traits\ListTrait;

/**
 * Class AsitList extends Asit, assert collection elements of expected valueType
 *
 * @package Kigkonsult\Asit
 */
class AsitList extends Asit implements ListTypeInterface
{
    use TypeTrait;

    use ListTrait;

    /**
     * Append typed element to (array) collection, opt with primary key
     *
     * @override
     * @param mixed                 $element
     * @param null|int|string       $pKey  MUST be unique
     * @param null|int|string|int[]|string[] $tags  not used here
     * @return static
     * @throws PkeyException
     * @throws TypeException
     */
    public function append(
        mixed $element,
        null|int|string $pKey = null,
        null|int|string|array $tags = null
    ) : static
    {
        $this->assertElementType( $element );
        parent::append( $element, $pKey );
        return $this;
    }
}
