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

use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\TagException;
use Kigkonsult\Asit\Exceptions\TypeException;
use Kigkonsult\Asit\Traits\TypeTrait;
use Kigkonsult\Asit\Traits\ListTrait;

/**
 * Class AsittagList extends Asittag, assert collection elements of preset valueType
 *
 * @package Kigkonsult\Asit
 */
class AsmittagList extends Asmittag implements ListTypeInterface
{
    use TypeTrait;

    use ListTrait;

    /**
     * Append typed element to (array) collection, opt with primary key
     *
     * @override
     * @param mixed            $element
     * @param int|string       $pKey    MUST be unique
     * @param int|string|array $tags    only int or string allowed
     * @return self
     * @throws PkeyException
     * @throws TagException
     * @throws TypeException
     */
    public function append( $element, $pKey = null, $tags = null ) : BaseInterface
    {
        if( $this->isValueTypeSet()) {
            $this->assertElementType( $element );
        }
        parent::append( $element, $pKey, $tags );
        return $this;
    }
}
