<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-24 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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

use Kigkonsult\Asit\Traits\TagTrait;
use Kigkonsult\Asit\Traits\PkeyTagTrait;

/**
 * Class Asittag extends asit, allow collection elements tags
 *
 * Also secondary keys, additional (non-unique) tags (aka attributes?)
 * may be set for each element. Tags are of int or string valueType.
 *
 * Collection elements are searchable using
 *     Iterator (et al.) methods
 *     primary key(s)
 *     tag(s)
 *     primary key(s) + tag(s)
 *
 * For non-assoc arrays,
 *     primary key is the (numeric) array index
 *     may also have tags
 *
 * @package    Kigkonsult\Asit
 */
class Asittag extends Asit
{
    use TagTrait;

    use PkeyTagTrait;

    /**
     * Overriden It/Asit methods
     */

    /**
     * Remove the current element
     *
     * @override
     * @return $this
     * @since 2.2.1 2024-01-08
     */
    public function remove() : static
    {
        foreach( $this->getCurrentTags() as $tag ) {
            $this->removeCurrentTag( $tag );
        }
        parent::remove();
        return $this;
    }
}
