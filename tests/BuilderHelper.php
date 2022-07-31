<?php

namespace Mehedi\WPQueryBuilderTestsExt;

use Mehedi\WPQueryBuilder\Query\Builder;
use Mehedi\WPQueryBuilder\Query\Grammar;
use Mehedi\WPQueryBuilder\Query\WPDB;

trait BuilderHelper
{
    public function builder()
    {
        return new Builder();
    }

    function initFakeDB()
    {
        Grammar::getInstance();

        WPDB::set(new FakeWPDB());
    }
}