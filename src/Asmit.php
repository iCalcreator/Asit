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
declare( strict_types = 1 );
namespace Kigkonsult\Asit;

use ArrayIterator;
use Kigkonsult\Asit\Exceptions\PkeyException;
use RuntimeException;
use Traversable;

use function array_keys;
use function count;
use function ksort;
use function sprintf;
use function strlen;

/**
 * Class Asmit extends Asit, allow collection element multiple primary keys
 *
 * Asmit has, along with SeekableIterator, Countable and IteratorAggregate (+Traversable) methods,
 * assoc array collection element get-/set-methods.
 * The assoc element array key is used as (unique) primary key.
 * Each collection element may have more than one primary key.
 *
 * The collection element may, as for Iterator (et al.), be of any valueType.
 *
 * Primary keys may be replaced by another (unique) key.
 *
 * Collection elements are searchable using
 *   Iterator (et al.) methods
 *   primary key(s)
 *
 * For non-assoc arrays,
 *   (first) primary key is the (numeric) array index
 *
 * Class AsmitList extends Asmit
 *   assert collection elements of expected valueType
 *
 * Class Asmittag extends Asmit
 *   Also secondary keys, additional (non-unique) tags (aka attributes?)
 *   may be set for each element.
 *
 * Class AsmittagList extends Asmittag
 *   assert collection elements of expected valueType
 *
 * @package    Kigkonsult\Asit
 */
class Asmit extends Asit
{
    /**
     * toString
     *
     * @return string
     */
    public function toString() : string
    {
        $string = self::$SP0;
        $pLen   = strlen((string) $this->count());
        $this->rewind();
        while( $this->valid()) {
            $key = self::prepKeyString( $this->key(), $pLen );
            foreach((array) $this->getCurrentPkey( false ) as $pKey ) {
                $string .= self::pKey2String($key, $pKey );
            }
            $string .= self::element2String( $key, $this->current());
            $this->next();
        }
        return $string;
    }

    /**
     * Primary key methods
     */

    /**
     * Set (another) unique primary key for a collection element
     *
     * @override
     * @param int|string $pKey 0 (zero) allowed
     * @param int        $index
     * @return self
     * @throws PkeyException
     */
    protected function setPkey( $pKey, int $index ) : BaseInterface
    {
        self::assertPkey( $pKey );
        switch( true ) {
            case ( ! $this->pKeyExists( $pKey )) :
                break;
            case ( $index == $this->pKeys[$pKey] ) :
                return $this;
            default :
                throw new PkeyException(
                    sprintf( PkeyException::$PKEYFOUND, $pKey, $this->pKeys[$pKey] )
                );
        } // end switch
        $this->pKeys[$pKey] = $index;
        ksort( $this->pKeys, SORT_REGULAR );
        return $this;
    }

    /**
     * Return count of primary keys for collection element
     *
     * @param int|string $pKey 0 (zero) allowed
     * @return int
     * @throws PkeyException
     */
    public function countPkey( $pKey ) : int
    {
        $this->assertPkeyExists( $pKey );
        return count(
            array_keys( $this->pKeys, $this->pKeys[$pKey], true )
        );
    }

    /**
     * Remove primary key for collection element but not last
     *
     * @param int|string $pKey
     * @return self
     * @throws PkeyException
     */
    public function removePkey( $pKey ) : self
    {
        $this->assertPkeyExists( $pKey );
        if( 1 < $this->countPkey( $pKey )) {
            unset( $this->pKeys[$pKey] );
        }
        return $this;
    }

    /**
     * Return pKey(s) for 'current', one (firstFound=true) or all (array)
     *
     * @override
     * @param bool $firstFound
     * @return int|string|array
     * @throws RuntimeException
     */
    public function getCurrentPkey( bool $firstFound = true )
    {
        $this->assertCurrent();
        return $firstFound
            ? self::search( $this->position, $this->pKeys )
            : array_keys( $this->pKeys, $this->position, true );
    }

    /**
     * Add another 'current' pKey
     *
     * setCurrentPkey() alias
     *
     * @param int|string $pKey
     * @return self
     * @throws PkeyException
     * @throws RuntimeException
     */
    public function addCurrentPkey( $pKey ) : self
    {
        $this->setCurrentPkey( $pKey );
        return $this;
    }

    /**
     * SeekableIterator, Countable, IteratorAggregate et al. methods
     */

    /**
     * Return an external iterator ( pKey => element ), Traversable
     *
     * In case of multiple primary keys for element, first found is used
     *
     * @override
     * @return Traversable
     */
    public function getPkeyIterator() : Traversable
    {
        $output = $ixList = [];
        foreach( $this->pKeys as $pKey => $pIx ) {
            if( ! isset( $ixList[$pIx] )) {
                $output[$pKey] = $this->collection[$pIx];
                $ixList[$pIx]  = $pIx;
            }
        } // end foreach
        return new ArrayIterator( $output );
    }
}
