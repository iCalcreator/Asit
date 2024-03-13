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
use Traversable;

/**
 * Interface PkeyInterface
 *
 * The pKey 'contract' methods
 *
 * @package Kigkonsult\Asit
 * @since 2.3.09 2024-03-12
 */
interface PkeyInterface
{
    /**
     * Return bool true if a primary key is found
     *
     * @param int|string $pKey
     * @return bool
     */
    public function pKeyExists( int | string $pKey ) : bool;

    /**
     * Return all primary keys
     *
     * @param null|int|string $dummy
     * @param null|int $sortFlag default SORT_REGULAR
     * @return int[]|string[]
     */
    public function getPkeys( null|int|string $dummy = null, ? int $sortFlag = SORT_REGULAR ) : array;

    /**
     * Replace (set) primary key for collection element
     *
     * @param int|string $oldPkey   0 (zero) allowed
     * @param int|string $newPkey   0 (zero) allowed
     * @return static
     * @throws PkeyException
     */
    public function replacePkey( int | string $oldPkey, int | string $newPkey ) : static;

    /**
     * Return (first found) pKey for 'current' element
     *
     * @return int|string
     * @throws PositionException
     */
    public function getCurrentPkey() : int|string;

    /**
     * Set (reset) primary key for 'current' element
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @param int|string $pKey   0 (zero) allowed
     * @return static
     * @throws PkeyException
     * @throws PositionException
     */
    public function setCurrentPkey( int | string $pKey ) :static;

    /**
     * Implements Generators yield functionality
     *
     * A memory minimizer for use in foreach-loops, replaces get(), getIterator() etc
     * Using (first found) pKey as key
     *
     * code>
     * foreach( $itInstance->yield() as [ $pKey => ] $value ) {
     *     ....
     * }
     * </code>
     *
     * @return mixed
     * @since 2.3.05 2024-01-08
     */
    public function pKeyYield() : mixed;

    /**
     * Return element for a primary key
     *
     * Alias for pKeySeek( pKey )->current()
     *
     * @param int|string $pKey
     * @param null|bool $makeCurrent    default false
     * @return mixed
     * @throws PkeyException
     * @since 2.3.04 2024-02-07
     */
    public function pKeyFetch( int|string $pKey, ? bool $makeCurrent = null ) : mixed;

    /**
     * Return (non-assoc) array of element(s) in collection
     *
     * Opt using primary keys and/or tag(s) for selection
     *
     * @param int|string|int[]|string[] $pKeys
     * @param null|int|callable $sortParam asort sort_flags or uasort callable
     *                                     (null, default, ksort)
     * @return mixed[]
     * @throws SortException
     * @since 2.3.01 2024-02-07
     */
    public function pKeyGet( int|string|array $pKeys = null, mixed $sortParam = null ) : array;

    /**
     * Return an external iterator ( pKey => element ), Traversable
     *
     * @return Traversable   mixed[]
     */
    public function getPkeyIterator() : Traversable;

    /**
     * Seeks to a given position in the iterator using pKey, i.e. make current
     *
     * @param int|string $pKey
     * @return static
     * @throws PkeyException
     */
    public function pKeySeek( int | string $pKey ) : static;
}
