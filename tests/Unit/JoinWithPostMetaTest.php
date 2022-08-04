<?php

namespace Mehedi\WPQueryBuilderTestsExt\Unit;

use Mehedi\WPQueryBuilder\Query\Join;
use Mehedi\WPQueryBuilderExt\Plugins\JoinPostWithMeta;
use Mehedi\WPQueryBuilderTestsExt\BuilderHelper;
use Mehedi\WPQueryBuilderTestsExt\FakeWPDB;
use PHPUnit\Framework\TestCase;

class JoinWithPostMetaTest extends TestCase
{
    use BuilderHelper;

    /**
     * @test
     */
    function it_can_apply_the_plugin()
    {
        $builder = $this->builder()->plugin(new JoinPostWithMeta());

        $this->assertEquals('posts', $builder->from);
        $this->assertCount(1, $builder->joins);
        $this->assertInstanceOf(Join::class, $builder->joins[0]);
        $this->assertEquals('postmeta', $builder->joins[0]->table);
        $this->assertEquals('postmeta', $builder->joins[0]->table);
        $this->assertEquals([
            "type" => "Column",
            "first" => "posts.ID",
            "operator" => "=",
            "second" => "postmeta.post_id",
            "boolean" => "and",
        ], $builder->joins[0]->wheres[0]);
    }

    /**
     * @test
     */
    function it_can_generate_currect_sql()
    {
        $this->initFakeDB();

        FakeWPDB::add('get_results', function ($sql) {
            $this->assertEquals('select * from wp_posts inner join wp_postmeta on wp_posts.ID = wp_postmeta.post_id limit 10', $sql);
        });

        $this->builder()->plugin(new JoinPostWithMeta())->limit(10)->get();
    }
}