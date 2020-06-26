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
namespace Kigkonsult\Asit\Traits;

use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

use function array_filter;
use function array_intersect;
use function array_keys;
use function array_merge;
use function array_search;
use function array_unique;
use function array_values;
use function in_array;
use function sort;
use function sprintf;
use function var_export;

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
     * Return bool true if element (identified by pKey) has tag(s), not found pKey/tag return false
     *
     * @param  int|string $pKey
     * @param  int|string|array $tag
     * @return bool
     */
    public function hasPkeyTag( $pKey, $tag ) {
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
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @throws PkeyException
     * @throws TagException
     */
    public function addPkeyTag( $pKey, $tag ) {
        if( ! $this->pKeyExists( $pKey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYNOTFOUND, var_export( $pKey, true )));
        }
        $this->addTag( $tag, $this->pKeys[$pKey] );
    }

    /**
     * Remove pKey/tag methods
     */

    /**
     * Remove tag (secondary key) for primary key element
     *
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws PkeyException
     */
    public function removePkeyTag( $pKey, $tag ) {
        if( ! $this->tagExists( $tag )) {
            return $this;
        }
        if( ! $this->pKeyExists( $pKey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYNOTFOUND, var_export( $pKey, true )));
        }
        $index = $this->pKeys[$pKey];
        if( ! in_array( $index, $this->tags[$tag] )) {
            return $this;
        }
        $tIx = array_search( $index, $this->tags[$tag] );
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
     * Return primary keys, primary keys for collection elements using tag or empty array on not found
     *
     * @override
     * @param int|string $tag  (0 (zero) allowed)
     * @param int $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getPkeys( $tag = null, $sortFlag = SORT_REGULAR ) {
        if( null === $tag ) {
            return parent::getPkeys( $sortFlag );
        }
        if( ! $this->tagExists( $tag )) {
            return [];
        }
        $pKeys = [];
        foreach( $this->tags[$tag] as $index ) {
            $pKeys[] = array_search( $index, $this->pKeys, true );
            if( $sortFlag != SORT_REGULAR ) {
                sort( $pKeys, $sortFlag );
            }
        }
        return $pKeys;
    }

    /**
     * Pkey/Tag get tag method
     */

    /**
     * Return all tags, tags for one collection element using the primary key or empty array on not found
     *
     * @param int|string $pKey
     * @param int        $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getTags( $pKey = null, $sortFlag = SORT_REGULAR ) {
        $tags = array_keys( $this->tags );
        if( null === $pKey ) {
            if( $sortFlag != SORT_REGULAR ) {
                sort( $tags, $sortFlag );
            }
            return $tags;
        }
        if( ! $this->pKeyExists( $pKey )) {
            return [];
        }
        $theIndex = $this->pKeys[$pKey];
        $result   = [];
        foreach( $tags as $tag ) {
            if( in_array( $theIndex, $this->tags[$tag] )) {
                $result[] = $tag;
            }
        }
        return $result;
    }

    /**
     * Return tags for one collection element using the primary key or empty array on not found
     *
     * Convenient getTags method alias
     *
     * @param  int|string $pKey
     * @param int        $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getPkeyTags( $pKey, $sortFlag = SORT_REGULAR ) {
        return $this->getTags( $pKey, $sortFlag );
    }

    /**
     * Pkey/Tag get element methods
     */

    /**
     * Return array sub-set of collection element (internal, int) indexes using tags or empty array on not found
     *
     * On incompatible tags, an empty array is returned
     *
     * @param  int|string|array $tags
     * @param  bool  $union
     * @return array
     */
    private function getTagIndexes( $tags, $union = true ) {
        $elementIxs = [];
        foreach((array) $tags as $tag ) {
            switch( true ) {
                case ( ! $this->tagExists( $tag )) :
                    continue 2;
//              case empty( $elementIxs ) :
                case ( 0 == count( $elementIxs )) :
                    $elementIxs = $this->tags[$tag];
                    continue 2;
                case $union :
                    $elementIxs = array_intersect( $elementIxs, $this->tags[$tag] );
                    if( 0 == count( $elementIxs )) {
                        return []; // incompatible tags
                    }
                    continue 2;
                default :
                    $elementIxs = array_unique( array_merge( $elementIxs, $this->tags[$tag] ), SORT_NUMERIC );
                    break;
            } // end switch
        } // end foreach
        return $elementIxs;
    }

    /**
     * Return (non-assoc) array of element(s) in collection, opt using primary keys and/or tag(s)
     *
     * using the opt. primary keys for selection
     * If tags are given,
     *     if union is bool true, the result collection element hits match all tags, false match any tag.
     * Hits with exclTags are excluded
     *
     * @override
     * @param  int|string|array $pKeys
     * @param  int|string|array $tags   none-used tag is skipped
     * @param  bool             $union
     * @param  int|string|array $exclTags
     * @param  int|callable     $sortParam    asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function get( $pKeys = null, $tags = null, $union = true, $exclTags = [], $sortParam = null ) {
        if( empty( $pKeys ) && empty( $tags ) ) {
            return ( null === $sortParam ) ? $this->collection : self::sort( $this->collection, $sortParam );
        }
        $indexes = [];
        if( null !== $pKeys ) {
            $indexes = $this->getPkeyIndexes((array) $pKeys );
            if( empty( $indexes ) ) { // pKeys not found, ignore tags
                return [];
            }
        } // end if
        $result = [];
        if( null === $tags ) { // pKeys found, no tags
            foreach( $indexes as $index ) {
                if( ! $this->hasTag( $index, $exclTags ) ) {
                    $result[$index] = $this->collection[$index];
                }
            } // end foreach
            return ( null === $sortParam ) ? $result : self::sort( $result, $sortParam );
        } // end if
        foreach( $this->getTagIndexes((array) $tags, $union ) as $tagIx ) {
            if( ( null === $pKeys ) || in_array( $tagIx, $indexes ) ) {
                $result[$tagIx] = $this->collection[$tagIx];
            }
        } // end foreach
        foreach( array_keys( $result ) as $hitIx ) {
            if( $this->hasTag( $hitIx, $exclTags ) ) {
                unset( $result[$hitIx] );
            }
        } // end foreach
        return ( null === $sortParam ) ? $result : self::sort( $result, $sortParam );
    }

    /**
     * Set element Pkey/tag methods
     */

    /**
     * Append element to (array) collection, opt with primary key and/or tags (secondary keys)
     *
     * Note, last appended element is always 'current'
     *
     * @override
     * @param mixed $element
     * @param int|string $pKey  MUST be unique
     * @param array $tags       only int or string allowed
     * @return static
     * @throws PkeyException
     * @throws TagException
     */
    public function append( $element, $pKey = null, $tags = null ) {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey )) {
            throw new PkeyException( sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ));
        }
        foreach((array) $tags as $tag ) {
            self::assertTag( $tag );
        }
        $this->collection[$index] = $element;
        $this->setPkey( $pKey, $index );
        foreach((array) $tags as $tag ) {
            $this->addTag( $tag, $index );
        }
        $this->position = $index;
        return $this;
    }

}