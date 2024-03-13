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

use InvalidArgumentException;
use Kigkonsult\Asit\Exceptions\PkeyException;
use Kigkonsult\Asit\Exceptions\PositionException;
use Kigkonsult\Asit\Exceptions\SortException;
use Kigkonsult\Asit\Exceptions\TagException;

use function array_key_exists;
use function count;
use function implode;
use function in_array;
use function is_int;
use function is_string;
use function ksort;
use function sprintf;
use function trim;

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
     * @var mixed[]    ( tag => int[]|string[] )
     */
    protected array $tags = [];

    /**
     * Clear (remove) collection
     *
     * @override
     * @return static
     */
    public function init() : static
    {
        $this->tags = [];
        parent::init();
        return $this;
    }

    /**
     * Assert tag, int and string allowed
     *
     * @param  mixed $tag
     * @return void
     * @throws TagException
     */
    public static function assertTag( mixed $tag ) : void
    {
        static $TMPL = "%s : Invalid tag : (%%s) %%s";
        $tmpl = sprintf( $TMPL, TagException::getClassName( static::class ));
        self::assertKey( $tag, TagException::class, $tmpl );
    }

    /**
     * Return key and tags as string
     *
     * @param string $key
     * @param int[]|string[]  $tags
     * @return string
     */
    protected static function tags2String( string $key, array $tags ) : string
    {
        static $TMPL  = "%s : (tags) %s ";
        static $COMMA = ", ";
        return sprintf( $TMPL, $key, implode( $COMMA, $tags )) . PHP_EOL;
    }

    /**
     * Get/has tag methods
     */

    /**
     * Return bool true if single or any tag in array is set
     *
     * @param int|string|int[]|string[] $tag
     * @return bool
     */
    public function tagExists( int | string | array $tag ) : bool
    {
        $found = false;
        foreach((array) $tag as $theTag ) {
            if( array_key_exists( $theTag, $this->tags )) {
                $found = true;
                break;
            }
        } // end foreach
        return $found;
    }

    /**
     * Return tags for 'current'
     *
     * To be used in parallel with the Iterator 'current' method, below
     *
     * @return int[]|string[]
     * @throws PositionException
     */
    public function getCurrentTags() : array
    {
        $this->assertCurrent();
        return $this->getTags( self::search( $this->position, $this->pKeys ));
    }

    /**
     * Return bool true if element (identified by index) has tag(s)
     *
     * Not found index return false (also invalid tag)
     *
     * @param int              $index
     * @param int|string|int[]|string[] $tag
     * @return bool
     */
    private function hasTag( int $index, int | string | array $tag ) : bool
    {
        switch( true ) {
            case is_int( $tag ) :
                break;
            case ( is_string( $tag ) && empty( trim( $tag ))) : // fall through
            case ( empty( $tag ) || ! isset( $this->collection[$index] )) :
                return false;
        } // end switch
        foreach((array) $tag as $theTag ) {
            if( $this->tagExists( $theTag ) &&
                in_array( $index, $this->tags[$theTag], true )) {
                return true;
            }
        } // end foreach
        return false;
    }

    /**
     * Return bool true if current has tag(s)
     *
     * To be used in parallel with the Iterator 'current' method
     *
     * @param int|string|int[]|string[] $tag
     * @return bool
     * @throws PositionException
     */
    public function hasCurrentTag( int | string | array $tag ) : bool
    {
        $this->assertCurrent();
        return $this->hasTag( $this->position, $tag );
    }

    /**
     * Return count of collection element using the tag, not found return 0
     *
     * @param int|string $tag
     * @return int
     */
    public function tagCount( int | string $tag ) : int
    {
        return $this->tagExists( $tag ) ? count( $this->tags[$tag] ) : 0;
    }

    /**
     * Add (set) tag methods
     */

    /**
     * Add tag (secondary key) for collection element
     *
     * @param int|string $tag  0 (zero) allowed also duplicates
     * @param int        $index
     * @return void
     * @throws TagException
     */
    protected function addTag( int | string $tag, int $index ) : void
    {
        self::assertTag( $tag );
        if( ! $this->tagExists( $tag )) {
            $this->tags[$tag] = [];
            ksort( $this->tags );
        }
        if( ! in_array( $index, $this->tags[$tag], true )) {
            $this->tags[$tag][] = $index;
            ksort( $this->tags[$tag] );
        }
    }

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
    public function addCurrentTag( int | string $tag ) : static
    {
        $this->assertCurrent();
        $this->addTag( $tag, $this->position );
        return $this;
    }

    /**
     * Remove tag method
     */

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
    public function removeCurrentTag( int | string $tag ) : static
    {
        $this->assertCurrent();
        $pKey = $this->getCurrentPkey();
        $this->removePkeyTag( $pKey, $tag );
        return $this;
    }

    /**
     * Pkey/Tag get element methods
     */

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
    ) : array
    {
        return $this->pKeyTagGet( null, $tags, $union, $exclTags, $sortParam );
    }
}
