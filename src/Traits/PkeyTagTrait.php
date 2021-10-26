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
namespace Kigkonsult\Asit\Traits;

use Kigkonsult\Asit\BaseInterface;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

use function array_filter;
use function array_intersect;
use function array_keys;
use function array_merge;
use function array_unique;
use function array_values;
use function in_array;
use function sort;

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
     * @param  int|string       $pKey
     * @param  int|string|array $tag
     * @return bool
     */
    public function hasPkeyTag( $pKey, $tag ) : bool
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
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return self
     * @throws PkeyException
     * @throws TagException
     */
    public function addPkeyTag( $pKey, $tag ) : BaseInterface
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
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return self
     * @throws PkeyException
     */
    public function removePkeyTag( $pKey, $tag ) : BaseInterface
    {
        if( ! $this->tagExists( $tag )) {
            return $this;
        }
        $this->assertPkeyExists( $pKey );
        $index = $this->pKeys[$pKey];
        if( ! in_array( $index, $this->tags[$tag], true ) ) {
            return $this;
        }
        $tIx = self::search( $index, $this->tags[$tag] );
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
     * Return primary keys, primary keys for collection elements using tag
     *
     * Empty array on not found
     *
     * @override  Asit::getPkeys()
     * @param mixed $tag
     * @param null|int $sortFlag   default SORT_REGULAR
     * @return array
     */
    public function getPkeys( $tag = null, ? int $sortFlag = SORT_REGULAR ) : array
    {
        if( empty( $tag )) {
            return parent::getPkeys( SORT_REGULAR );
        }
        if( ! $this->tagExists( $tag )) {
            return [];
        }
        $pKeys = [];
        foreach( $this->tags[$tag] as $index ) {
            $pKeys[] = self::search( $index, $this->pKeys );
        }
        sort( $pKeys, $sortFlag ?? SORT_REGULAR );
        return $pKeys;
    }

    /**
     * Pkey/Tag get tag method
     */

    /**
     * Return all tags, tags for one collection element using the primary key
     *
     * Empty array on not found
     *
     * @param int|string $pKey
     * @param null|int   $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getTags( $pKey = null, ? int $sortFlag = SORT_REGULAR ) : array
    {
        $tags = array_keys( $this->tags );
        if( null === $pKey ) {
            sort( $tags, $sortFlag ?? SORT_REGULAR );
            return $tags;
        }
        if( ! $this->pKeyExists( $pKey )) {
            return [];
        }
        $theIndex = $this->pKeys[$pKey];
        $result   = [];
        foreach( $tags as $tag ) {
            if( in_array( $theIndex, $this->tags[$tag], true ) ) {
                $result[] = $tag;
            }
        } // end foreach
        return $result;
    }

    /**
     * Return tags for one collection element using the primary key
     *
     * Empty array on not found
     * Convenient getTags method alias
     *
     * @param int|string $pKey
     * @param int         $sortFlag  default SORT_REGULAR
     * @return array
     */
    public function getPkeyTags( $pKey, int $sortFlag = SORT_REGULAR ) : array
    {
        return $this->getTags( $pKey, $sortFlag );
    }

    /**
     * Pkey/Tag get element methods
     */

    /**
     * Return array sub-set of collection element (internal, int) indexes using tags
     *
     * Return empty array on not found or for incompatible tags
     *
     * @param  int|string|array $tags
     * @param bool|null $union
     * @return array
     */
    private function getTagIndexes( $tags, ?bool $union = true ) : array
    {
        $elementIxs = [];
        foreach((array) $tags as $tag ) {
            switch( true ) {
                case ( ! $this->tagExists( $tag )) :
                    continue 2;
                case ( 0 === count( $elementIxs )) :
                    $elementIxs = $this->tags[$tag];
                    continue 2;
                case $union :
                    $elementIxs = array_intersect(
                        $elementIxs,
                        $this->tags[$tag]
                    );
                    if( 0 === count( $elementIxs )) {
                        return []; // incompatible tags
                    }
                    continue 2;
                default :
                    $elementIxs = array_unique(
                        array_merge( $elementIxs, $this->tags[$tag] ),
                        SORT_NUMERIC
                    );
                    break;
            } // end switch
        } // end foreach
        return $elementIxs;
    }

    /**
     * Return (non-assoc) array of element(s) in collection
     *
     * Using the opt. primary keys for selection and/or tags
     * If tags are given,
     *     if union is bool true,
     *     the result collection element hits match all tags,
     * false match any tag.
     * Hits with exclTags are excluded
     *
     * @override
     * @param  int|string|array $pKeys
     * @param  int|string|array $tags       none-used tag is skipped
     * @param  null|bool        $union
     * @param  null|int|string|array $exclTags
     * @param  null|int|callable     $sortParam  asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function pKeyTagGet(
        $pKeys = null,
        $tags = null,
        ? bool $union = true,
        $exclTags = [],
        $sortParam = null
    ) : array
    {
        if( empty( $pKeys ) && empty( $tags )) {
            return ( null === $sortParam )
                ? $this->collection
                : self::sort( $this->collection, $sortParam );
        }
        $indexes = [];
        if( null !== $pKeys ) {
            $indexes = $this->getPkeyIndexes((array) $pKeys );
            if( empty( $indexes )) { // pKeys not found, ignore tags
                return [];
            }
        } // end if
        $result = [];
        if( null === $tags ) { // pKeys found, no tags
            foreach( $indexes as $index ) {
                if( ! $this->hasTag( $index, $exclTags )) {
                    $result[$index] = $this->collection[$index];
                }
            } // end foreach
            return ( null === $sortParam )
                ? $result
                : self::sort( $result, $sortParam );
        } // end if
        foreach( $this->getTagIndexes((array) $tags, $union ) as $tagIx ) {
            if( ( null === $pKeys ) || in_array( $tagIx, $indexes, true ) ) {
                $result[$tagIx] = $this->collection[$tagIx];
            }
        } // end foreach
        foreach( array_keys( $result ) as $hitIx ) {
            if( $this->hasTag( $hitIx, $exclTags )) {
                unset( $result[$hitIx] );
            }
        } // end foreach
        return ( null === $sortParam )
            ? $result
            : self::sort( $result, $sortParam );
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
     * @param mixed            $element
     * @param int|string       $pKey  MUST be unique
     * @param int|string|array $tags  only int or string allowed
     * @return self
     * @throws PkeyException
     * @throws TagException
     */
    public function append( $element, $pKey = null, $tags = null ) : BaseInterface
    {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        else {
            self::assertPkey( $pKey );
            $this->assertPkeyNotExists( $pKey );
        }
        $tags = (array) $tags;
        foreach( $tags as $tag ) {
            self::assertTag( $tag );
        }
        $this->setPkey( $pKey, $index );
        $this->collection[$index] = $element;
        foreach( $tags as $tag ) {
            $this->addTag( $tag, $index );
        }
        $this->position = $index;
        return $this;
    }
}
