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

use Kigkonsult\Asit\Traits\TagTrait;
use Kigkonsult\Asit\Traits\PkeyTagTrait;

use function strlen;

/**
 * Class Asittag extends asit, allow collection elements tags
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
class Asmittag extends Asmit
{
    use TagTrait;

    use PkeyTagTrait;

    /**
     * toString
     *
     * @return string
     */
    public function toString()
    {
        $string = self::$SP0;
        $pLen   = strlen((string) $this->count());
        $this->rewind();
        while( $this->valid()) {
            $key = self::prepKeyString( $this->key(), $pLen );
            foreach( $this->getCurrentPkey( false ) as $pKey ) {
                $string .= self::pKey2String($key, $pKey );
            }
            $string .= self::tags2String( $key, $this->getCurrentTags());
            $string .= self::element2String( $key, $this->current());
            $this->next();
        }
        return $string;
    }
}
