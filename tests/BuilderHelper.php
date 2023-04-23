<?php

namespace Mehedi\WPQueryBuilderTestsExt;

use Mehedi\WPQueryBuilder\Query\Builder;

trait BuilderHelper
{
    public function builder()
    {
        return new Builder();
    }
}