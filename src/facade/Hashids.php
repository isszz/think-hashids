<?php
declare (strict_types = 1);

namespace isszz\hashids\facade;

use think\Facade;

class Hashids extends Facade
{
    protected static function getFacadeClass()
    {
        return \isszz\hashids\Hashids::class;
        return 'isszz.hashids';
    }
}
