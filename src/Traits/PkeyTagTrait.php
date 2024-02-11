<?php
/**
 * Asit package manages array collections
 *
 * This file is part of Asit.
 *
 * @author    Kjell-Inge Gustafsson, kigkonsult <ical@kigkonsult.se>
 * @copyright 2020-2024 Kjell-Inge Gustafsson, kigkonsult, All rights reserved
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
namespace Kigkonsult\Asit\Traits;

use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

use function array_filter;
use function array_intersect;
use function array_keys;
use function array_unique;
use function array_values;
use function count;
use function in_array;
use function is_array;

/**
 * Trait PkeyTagTrait, pKey/tag methods
 *
 * @package Kigkonsult\Asit
 */
trait PkeyTagTrait
{
    /**
     * Get/has pKey/tag methods
     */

    /**
     * Return bool true if element (identified by pKey) has tag(s)
     *
     * Not found pKey/tag return false
     *
     * @param int|string $pKey
     * @param int|string|int[]|string[] $tag
     * @return bool
     */
    public function hasPkeyTag( int|string $pKey, array|int|string $tag ) : bool
    {
        if( ! $this->pKeyExists( $pKey )) {
            return false;
        }
        return $this->hasTag( $this->pKeys[$pKey], $tag );
    }

    /**
     * Add (set) pKey/tag methods
     */

    /**
     * Add tag (secondary key) for primary key element
     *
     * @param int|string $pKey
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     * @throws TagException
     */
    public function addPkeyTag( int|string $pKey, int|string $tag ) : static
    {
        $this->assertPkeyExists( $pKey );
        $this->addTag( $tag, $this->pKeys[$pKey] );
        return $this;
    }

    /**
     * Remove pKey/tag methods
     */

    /**
     * Remove tag (secondary key) for primary key element
     *
     * @param int|string $pKey
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     */
    public function removePkeyTag( int|string $pKey, int|string $tag ) : static
    {
        if( ! $this->tagExists( $tag )) {
            return $this;
        }
        $this->assertPkeyExists( $pKey );
        $position = $this->pKeys[$pKey];
        if( ! in_array( $position, $this->tags[$tag], true )) {
            return $this;
        }
        $tIx   = self::search( $position, $this->tags[$tag] );
        unset( $this->tags[$tag][$tIx] );
        $this->tags[$tag] = array_values( $this->tags[$tag] );
        if( empty( $this->tags[$tag] )) {
            unset( $this->tags[$tag] );
            $this->tags = array_filter( $this->tags );
        } // end if
        return $this;
    }

    /**
     * Pkey/Tag get pkey method
     */

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
    public function getPkeys( int|string $tag = null, ? int $sortFlag = null ) : array
    {
        if( empty( $tag )) {
            return self::sort( parent::getPkeys(), $sortFlag );
        }
        if( ! $this->tagExists( $tag )) {
            return [];
        }
        $pKeys = [];
        foreach( $this->tags[$tag] as $position ) {
            foreach( $this->pKeys as $pKey => $pKeyPos ) {
                if( $position === $pKeyPos ) {
                    $pKeys[] = $pKey;
                }
            }
        }
        $sortFlag = $sortFlag ?? SORT_REGULAR;
        $pKeys = array_unique( $pKeys, $sortFlag );
        return self::sort( $pKeys, $sortFlag );
    }

    /**
     * Pkey/Tag get tag method
     */

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
    public function getTags( null|bool|int|string $pKey = null, ? int $sortFlag = SORT_REGULAR ) : array
    {
        $tags = array_keys( $this->tags );
        switch( true ) {
            case (( null === $pKey ) || ( false === $pKey )) :
                return self::sort( $tags, $sortFlag );
            case ( true === $pKey ) :
                throw new PkeyException( PkeyException::$PKEYERR1 );
            case ! $this->pKeyExists( $pKey ) :
                return [];
        } // end switch
        $theIndex = $this->pKeys[$pKey];
        $result   = [];
        foreach( $tags as $tag ) {
            if( in_array( $theIndex, $this->tags[$tag], true )) {
                $result[] = $tag;
            }
        } // end foreach
        return self::sort( $result, $sortFlag );
    }

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
    public function getPkeyTags( int|string $pKey, ? int $sortFlag = SORT_REGULAR ) : array
    {
        return $this->getTags( $pKey, $sortFlag );
    }

    /**
     * Pkey/Tag get element methods
     */

    /**
     * Return array of sub-set of collection element (internal, int) indexes using tags
     *
     * Return empty array on not found or for incompatible tags
     *
     * @param int|string|int[]|string[] $tags
     * @param bool|null $union
     * @return int[]
     */
    protected function getTagIndexes( array|int|string $tags, ? bool $union = true ) : array
    {
        $elementIxs = [];
        if( null === $union ) {
            $union = true;
        }
        foreach((array) $tags as $tag ) {
            try {
                self::assertTag( $tag );
            }
            catch( TagException ) {
                continue;
            }
            switch( true ) {
                case ! $this->tagExists( $tag ) :
                    break;
                case ( 0 === count( $elementIxs )) :
                    $elementIxs = $this->tags[$tag];
                    break;
                case $union :
                    $elementIxs = array_intersect(
                        $elementIxs,
                        $this->tags[$tag]
                    );
                    if( 0 === count( $elementIxs )) {
                        return []; // incompatible tags
                    }
                    break;
                default :
                    foreach( $this->tags[$tag] as $ix ) {
                        $elementIxs[] = (int) $ix;
                    }
                    break;
            } // end switch
        } // end foreach
        $elementIxs = array_unique( $elementIxs, SORT_NUMERIC );
        sort( $elementIxs );
        return $elementIxs;
    }

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
    ) : array
    {
        if( empty( $pKeys ) && empty( $tags )) {
            return self::sort( $this->collection, $sortParam );
        }
        $indexes = $result = [];
        if( null !== $pKeys ) {
            $indexes = $this->getPkeyIndexes((array) $pKeys );
            if( empty( $indexes )) { // pKeys not found, ignore tags
                return [];
            }
        } // end if
        if( null === $tags ) { // pKeys found, no search tags
            $this->copyElements( $indexes, $result );
            return self::sort( $this->optWithoutExcludedTags( $result, $exclTags ), $sortParam );
        } // end if
        foreach( $this->getTagIndexes((array) $tags, $union ) as $tagIx ) {
            if(( null === $pKeys ) || in_array( $tagIx, $indexes, true )) {
                $result[$tagIx] = $this->collection[$tagIx];
            }
        } // end foreach
        return self::sort(  $this->optWithoutExcludedTags( $result, $exclTags ), $sortParam );
    }

    /**
     * Return results without opt excluded tags
     *
     * @param mixed[] $result
     * @param null|int|string|int[]|string[] $exclTags
     * @return mixed[]
     */
    protected function optWithoutExcludedTags(
        array $result,
        null|int|string|array $exclTags = null
    ) : array
    {
        if( empty( $exclTags )) {
            return $result;
        }
        $output = [];
        foreach( array_keys( $result ) as $hitIx ) {
            if( $this->hasTag( (int) $hitIx, $exclTags )) {
                continue;
            }
            $output[$hitIx] = $result[$hitIx];
        } // end foreach
        return $output;
    }

    /**
     * Set element Pkey/tag methods
     */

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
    ) : static
    {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        else {
            self::assertPkey( $pKey );
            $this->assertPkeyNotExists( $pKey );
        }
        $this->collection[$index] = $element;
        $this->setPkey( $pKey, $index );
        $this->position = $index;
        switch( true ) {
            case ( null === $tags ) :
                break;
            case ! is_array( $tags ) :
                if( ! empty( $tags ) || ( '0' !== $tags )) {
                    $this->addTag( $tags, $index );
                }
                break;
            default : // array
                foreach( $tags as $tag ) {
                    $this->addTag( $tag, $index );
                }
                break;
        } // end switch
        return $this;
    }
}
