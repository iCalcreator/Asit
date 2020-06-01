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
namespace Kigkonsult\Asit;

use InvalidArgumentException;
use RuntimeException;

/**
 * Class Asittag extends asit
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
class Asittag
     extends Asit
{

    /**
     * Secondary keys (attributes), here tags, for collection element
     *
     * Named 'tag' here to avoid mixup with Iterator method 'key'
     * Each tag may exist on multiple collection elements
     *
     * @var array
     */
    protected $tags = [];

    /**
     * Extended primary key methods
     */

    /**
     * Return all primary keys, primary keys for collection elements using tag or empty array on not found
     *
     * @override
     * @param int|string $tag  (0 (zero) allowed)
     * @param int $sort  default SORT_REGULAR
     * @return array
     */
    public function getPkeys( $tag = null, $sort = SORT_REGULAR ) {
        if( null === $tag ) {
            return parent::getPkeys( $sort );
        }
        if( ! $this->tagExists( $tag )) {
            return [];
        }
        $pKeys = [];
        foreach( $this->tags[$tag] as $index ) {
            $pKeys[] = array_search( $index, $this->pKeys, true );
            if( $sort != SORT_REGULAR ) {
                sort( $pKeys, $sort );
            }
        }
        return $pKeys;
    }

    /**
     * Tag methods
     */

    /**
     * Assert tag, int and string allowed
     *
     * @param  mixed $tag
     * @return void
     */
    public static function assertTag( $tag ) {
        static $ERR = 'Invalid tag : %s';
        self::assertKey( $tag, $ERR );
    }

    /**
     * Return bool true if single or any tag in array are set
     *
     * @param  int|string|array $tag
     * @return bool
     */
    public function tagExists( $tag ) {
        $found = false;
        foreach((array) $tag as $theTag ) {
            if( array_key_exists( $theTag, $this->tags )) {
                $found = true;
            }
        }
        return $found;
    }

    /**
     * Return all tags, tags for one collection element using the primary key or empty array on not found
     *
     * @param int|string $pKey
     * @param int        $sort  default SORT_REGULAR
     * @return array
     */
    public function getTags( $pKey = null, $sort = SORT_REGULAR ) {
        $tags = array_keys( $this->tags );
        if( null === $pKey ) {
            if( $sort != SORT_REGULAR ) {
                sort( $tags, $sort );
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
     * @param int        $sort  default SORT_REGULAR
     * @return array
     */
    public function getPkeyTags( $pKey, $sort = SORT_REGULAR ) {
        return $this->getTags( $pKey, $sort );
    }

    /**
     * Return tags for 'current'
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @return array
     * @throws RuntimeException
     */
    public function getCurrentTags() {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        return $this->getTags( array_search( $this->position, $this->pKeys, true ));
    }

    /**
     * Return bool true if element (identified by index) has tag(s), not found index return false
     *
     * @param  int $index
     * @param  int|string|array $tag
     * @return bool
     */
    private function hasTag( $index, $tag ) {
        if( empty( $tag ) || ! isset( $this->collection[$index] )) {
            return false;
        }
        foreach((array) $tag as $theTag ) {
            if( $this->tagExists( $theTag ) && in_array( $index, $this->tags[$theTag] )) {
                return true;
            }
        }
        return false;
    }

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
     * Return bool true if current has tag(s)
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param  int|string|array $tag
     * @return bool
     * @throws RuntimeException
     */
    public function hasCurrentTag( $tag ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        return $this->hasTag( $this->position, $tag );
    }

    /**
     * Return count of collection element using the tag, not found return 0
     *
     * @param  int|string $tag
     * @return bool
     */
    public function tagCount( $tag ) {
        return $this->tagExists( $tag ) ? count( $this->tags[$tag] ) : 0;
    }

    /**
     * Add tag (secondary key) for collection element
     *
     * @param mixed $tag  0 (zero) allowed also duplicates
     * @param int $index
     * @throws InvalidArgumentException
     */
    private function addTag( $tag, $index ) {
        self::assertTag( $tag );
        if( ! $this->tagExists( $tag )) {
            $this->tags[$tag] = [];
            ksort( $this->tags );
        }
        if( ! in_array( $index, $this->tags[$tag] )) {
            $this->tags[$tag][] = $index;
            ksort( $this->tags[$tag], SORT_REGULAR );
        }
    }

    /**
     * Add tag (secondary key) for primary key element
     *
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @throws InvalidArgumentException
     */
    public function addPkeyTag( $pKey, $tag ) {
        if( ! $this->pKeyExists( $pKey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYNOTFOUND, var_export( $pKey, true )));
        }
        $this->addTag( $tag, $this->pKeys[$pKey] );
    }

    /**
     * Add tag (secondary key) for 'current'
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function addCurrentTag( $tag ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        $this->addTag( $tag, $this->position );
    }

    /**
     * Remove tag (secondary key) for primary key element
     *
     * @param  int|string $pKey
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws InvalidArgumentException
     */
    public function removePkeyTag( $pKey, $tag ) {
        if( ! $this->tagExists( $tag )) {
            return $this;
        }
        if( ! $this->pKeyExists( $pKey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYNOTFOUND, var_export( $pKey, true )));
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
            $tags = [];
            foreach( $this->tags as $tag => $ixArr ) {
                if( ! empty( $ixArr )) {
                    $tags[$tag] = $ixArr;
                }
            }
        } // end if
        return $this;
    }

    /**
     * Remove tag (secondary key) for current
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function removeCurrentTag( $tag ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        return $this->removePkeyTag( $this->getCurrentPkey(), $tag );
    }

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
     * Get-methods
     */

    /**
     * Return (non-assoc) array of element(s) in collection, opt using primary keys and/or tag(s)
     *
     * If primary keys are given, the return collection element includes only these matching the primary keys.
     * Then, and if tags are given,
     *     if union is bool true, the result collection element hits match all tags, false match any tag.
     * Hits with exclTags are excluded
     *
     * @override
     * @param  int|string|array $pKeys
     * @param  int|string|array $tags   none-used tag is skipped
     * @param  bool  $union
     * @param  int|string|array $exclTags
     * @return array
     */
    public function get( $pKeys = null, $tags = null, $union = true, $exclTags = [] ) {
        if( empty( $pKeys ) && empty( $tags )) {
            return $this->collection;
        }
        $indexes = [];
        if( null !== $pKeys ) {
            $indexes = $this->getPkeyIndexes((array) $pKeys );
            if( empty( $indexes )) { // pKeys not found, ignore tags
                return [];
            }
        } // end if
        $result = [];
        if( null === $tags ) { // no tags
            foreach( $indexes as $index ) {
                if( ! $this->hasTag( $index, $exclTags )) {
                    $result[$index] = $this->collection[$index];
                }
            } // end foreach
            return $result;
        } // end if
        foreach( $this->getTagIndexes((array) $tags, $union ) as $tagIx ) {
            if(( null === $pKeys ) || in_array( $tagIx, $indexes )) {
                $result[$tagIx] = $this->collection[$tagIx];
            }
        } // end foreach
        foreach( array_keys( $result ) as $hitIx ) {
            if( $this->hasTag( $hitIx, $exclTags )) {
                unset( $result[$hitIx] );
            }
        } // end foreach
        return $result;
    }

    /**
     * Return (non-assoc array) sub-set of element(s) in collection using tags
     *
     * If union is bool true, the result collection element hits match all tags, false match any tag.
     * Convenient get method alias
     *
     * @param  int|string|array $tags
     * @param  bool  $union
     * @param  int|string|array $exclTags
     * @return array
     */
    public function tagGet( $tags, $union = true, $exclTags = [] ) {
        return $this->get( null, $tags, $union, $exclTags );
    }

    /**
     * Set-methods
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
     * @throws InvalidArgumentException
     */
    public function append( $element, $pKey = null, array $tags = [] ) {
        $index = $this->count();
        if( null === $pKey ) {
            $pKey = $index;
        }
        self::assertPkey( $pKey );
        if( $this->pKeyExists( $pKey )) {
            throw new InvalidArgumentException( sprintf( self::$PKEYFOUND, $pKey, $this->pKeys[$pKey] ));
        }
        foreach( $tags as $tag ) {
            self::assertTag( $tag );
        }
        $this->collection[$index] = $element;
        $this->setPkey( $pKey, $index );
        foreach( $tags as $tag ) {
            $this->addTag( $tag, $index );
        }
        $this->position = $index;
        return $this;
    }

}
