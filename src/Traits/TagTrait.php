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

use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;
use RuntimeException;

use function array_key_exists;
use function array_search;
use function count;
use function implode;
use function in_array;
use function ksort;
use function sprintf;

/**
 * Trait TagTrait, property and methods for tags
 *
 * @package Kigkonsult\Asit
 */
trait TagTrait
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
     * Clear (remove) collection
     *
     * @override
     * @return static
     */
    public function init() {
        $this->tags = [];
        return parent::init();
    }

    /**
     * Assert tag, int and string allowed
     *
     * @param  mixed $tag
     * @return void
     * @throws TagException
     */
    public static function assertTag( $tag ) {
        static $ERR = 'Invalid tag : %s';
        try {
            self::assertKey( $tag, $ERR );
        }
        catch( InvalidArgumentException $e ) {
            throw new TagException( $e->getMessage(), 20, $e );
        }
    }

    /**
     * Return key and tags as string
     *
     * @param string $key
     * @param array  $tags
     * @return string
     */
    protected static function tags2String( $key, array $tags ) {
        static $ROWtags = '%s : (tags) %s ';
        static $COMMA   = ', ';
        return sprintf( $ROWtags, $key, implode( $COMMA, $tags )) . PHP_EOL;
    }

    /**
     * Get/has tag methods
     */

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
     * Add (set) tag methods
     */

    /**
     * Add tag (secondary key) for collection element
     *
     * @param mixed $tag  0 (zero) allowed also duplicates
     * @param int $index
     * @throws TagException
     */
    private function addTag( $tag, $index ) {
        self::assertTag( $tag );
        if( ! $this->tagExists( $tag )) {
            $this->tags[$tag] = [];
            ksort( $this->tags, SORT_REGULAR );
        }
        if( ! in_array( $index, $this->tags[$tag] )) {
            $this->tags[$tag][] = $index;
            ksort( $this->tags[$tag], SORT_REGULAR );
        }
    }

    /**
     * Add tag (secondary key) for 'current'
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws TagException
     */
    public function addCurrentTag( $tag ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        $this->addTag( $tag, $this->position );
    }

    /**
     * Remove tag methods
     */

    /**
     * Remove tag (secondary key) for current
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string  $tag  0 (zero) allowed also duplicates
     * @return static
     * @throws RuntimeException
     */
    public function removeCurrentTag( $tag ) {
        if( ! $this->valid()) {
            throw new RuntimeException( self::$CURRENTNOTVALID );
        }
        return $this->removePkeyTag( $this->getCurrentPkey(), $tag );
    }

    /**
     * Pkey/Tag get element methods
     */


    /**
     * Return (non-assoc array) sub-set of element(s) in collection using tags
     *
     * If union is bool true, the result collection element hits match all tags, false match any tag.
     * Convenient get method alias
     *
     * @param  int|string|array $tags
     * @param  bool  $union
     * @param  int|string|array $exclTags
     * @param  int|callable $sortParam    asort sort_flags or uasort callable
     * @return array
     * @throws SortException
     */
    public function tagGet( $tags, $union = true, $exclTags = [], $sortParam = null ) {
        return $this->get( null, $tags, $union, $exclTags, $sortParam );
    }

}