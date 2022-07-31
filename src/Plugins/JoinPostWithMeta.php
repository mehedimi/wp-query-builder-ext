<?php

namespace Mehedi\WPQueryBuilderExt\Plugins;

use Mehedi\WPQueryBuilder\Contracts\Pluggable;
use Mehedi\WPQueryBuilder\Query\Builder;

class JoinPostWithMeta implements Pluggable
{
    protected $type;

    public function __construct($type = 'inner')
    {
        $this->type = $type;
    }

    /**
     * Joining posts table with post_meta
     *
     * @param Builder $builder
     *
     * @return void
     */
    public function apply(Builder $builder)
    {
        $builder->from('posts')
            ->join('postmeta', 'posts.ID', '=', 'postmeta.post_id', $this->type);
    }
}