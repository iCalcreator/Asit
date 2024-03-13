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
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

/**
 * Interface PkeyTagInterface
 *
 * The pKey+Tag 'contract' methods
 *
 * @package Kigkonsult\Asit
 * @since 2.3.10 2024-03-12
 */
interface PkeyTagInterface
{
    /**
     * Return bool true if element (identified by pKey) has tag(s)
     *
     * Not found pKey/tag return false
     *
     * @param int|string $pKey
     * @param int|string|int[]|string[] $tag
     * @return bool
     */
    public function hasPkeyTag( int|string $pKey, array|int|string $tag ) : bool;

    /**
     * Add tag (secondary key) for primary key element
     *
     * @param int|string $pKey
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     * @throws TagException
     */
    public function addPkeyTag( int|string $pKey, int|string $tag ) : static;

    /**
     * Remove tag (secondary key) for primary key element
     *
     * @param int|string $pKey
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     */
    public function removePkeyTag( int|string $pKey, int|string $tag ) : static;

    /**
     * Return primary keys, primary keys for collection elements using (single) tag
     *
     * Empty array on not found
     *
     * @override  Asit::getPkeys()
     * @param null|int|string $tag
     * @param null|int        $sortFlag default SORT_REGULAR
     * @return int[]|string[]
     */
    public function getPkeys( int|string $tag = null, ? int $sortFlag = null ) : array;

    /**
     * Return all tags or tags for one collection element using a primary key
     *
     * Empty array on not found
     *
     * @param null|false|int|string $pKey
     * @param null|int   $sortFlag  default SORT_REGULAR
     * @return int[]|string[]
     * @throws PkeyException
     */
    public function getTags( null|bool|int|string $pKey = null, ? int $sortFlag = SORT_REGULAR ) : array;

    /**
     * Return tags for one collection element using the primary key
     *
     * Empty array on not found
     * Convenient getTags method alias
     *
     * @param int|string $pKey
     * @param null|int   $sortFlag  default SORT_REGULAR
     * @return int[]|string[]
     */
    public function getPkeyTags( int|string $pKey, ? int $sortFlag = SORT_REGULAR ) : array;

    /**
     * Return (non-assoc) array of element(s) in collection
     *
     * Using the opt primary keys for selection and/or (opt) tags
     * If tags are given,
     *     and union is bool true,
     *        the result collection element hits match all tags
     *     If union=false, results elements match any tag.
     * Hits with exclTags are excluded
     *
     * @override
     * @param null|int|string|int[]|string[] $pKeys
     * @param null|int|string|int[]|string[] $tags       none-used/found tag(s) are skipped
     * @param null|bool             $union               true=all tag must match, false=NOT
     * @param null|int|string|int[]|string[] $exclTags   tags to exclude
     * @param null|int|callable     $sortParam           asort sort_flags or uasort callable
     *                                                  (null=default, ksort))
     * @return mixed[]   with collection indexes as keys
     * @throws SortException
     */
    public function pKeyTagGet(
        null|int|string|array $pKeys = null,
        null|int|string|array $tags = null,
        ? bool                $union = true,
        null|int|string|array $exclTags = null,
        mixed                 $sortParam = null
    ) : array;

    /**
     * Append element to (array) collection
     *
     * Opt with primary key and/or tags (secondary keys)
     * Note, last appended element is always 'current'
     *
     * @override
     * @param mixed                 $element
     * @param null|int|string       $pKey  MUST be unique
     * @param null|int|string|int[]|string[] $tags  only int or string allowed
     * @return static
     * @throws PkeyException
     * @throws TagException
     */
    public function append(
        mixed           $element,
        null|int|string $pKey = null,
        null|int|string|array $tags = null
    ) : static;
}
