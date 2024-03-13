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
use Kigkonsult\Asit\Exceptions\PositionException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

/**
 * Interface TagInterface
 *
 * The tag 'contract' methods
 *
 * @package Kigkonsult\Asit
 * @since 2.3.08 2024-03-12
 */
interface TagInterface
{
    /**
     * Return bool true if single or any tag in array is set
     *
     * @param int|string|int[]|string[] $tag
     * @return bool
     */
    public function tagExists( int | string | array $tag ) : bool;

    /**
     * Return tags for 'current'
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @return int[]|string[]
     * @throws PositionException
     */
    public function getCurrentTags() : array;

    /**
     * Return bool true if current has tag(s)
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string|int[]|string[] $tag
     * @return bool
     * @throws PositionException
     */
    public function hasCurrentTag( int | string | array $tag ) : bool;

    /**
     * Return count of collection element using the tag, not found return 0
     *
     * @param int|string $tag
     * @return int
     */
    public function tagCount( int | string $tag ) : int;

    /**
     * Add tag (secondary key) for 'current'
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PositionException
     * @throws TagException
     */
    public function addCurrentTag( int | string $tag ) : static;

    /**
     * Remove tag (non-unique key) for current
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     * @throws PositionException
     */
    public function removeCurrentTag( int | string $tag ) : static;

    /**
     * Return (non-assoc array) sub-set of element(s) in collection using tags
     *
     * If union is bool true,
     *     the result collection element hits match all tags,
     *     false match any tag.
     * Convenient get method alias
     *
     * @param int|string|int[]|string[]  $tags
     * @param bool|null          $union
     * @param  int|string|int[]|string[] $exclTags
     * @param  null|int|callable $sortParam    asort sort_flags or uasort callable
     * @return mixed[]
     * @throws SortException
     */
    public function tagGet(
        array | int | string $tags,
        ? bool               $union = true,
        mixed                $exclTags = [],
        mixed                $sortParam = null
    ) : array;

}
