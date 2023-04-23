<?php

namespace Mehedi\WPQueryBuilderTestsExt\Unit;

use Mehedi\WPQueryBuilder\Connection;
use Mehedi\WPQueryBuilder\Query\Builder;
use Mehedi\WPQueryBuilder\Query\Grammar;
use Mehedi\WPQueryBuilderExt\Relations\WithTaxonomy;
use Mehedi\WPQueryBuilderTestsExt\BuilderHelper;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class TaxonomyTest extends TestCase
{
    use BuilderHelper;

    /**
     * @test
     */
    function it_can_add_relation()
    {
        $builder = $this->builder()->withRelation(new WithTaxonomy('taxonomies', $this->builder()));

        $this->assertInstanceOf(WithTaxonomy::class, $builder->with[0]);
    }

    /**
     * @test
     */
    function it_can_generate_correct_sql_query()
    {
        Grammar::getInstance()->setTablePrefix('wp_');

        $c = m::mock(Connection::class);

        $c->shouldReceive('select')->andReturn([]);

        $b = new Builder($c);

        $sql = 'select wp_terms.*, wp_term_taxonomy.count, wp_term_taxonomy.taxonomy, wp_term_relationships.object_id from wp_terms inner join wp_term_relationships on wp_terms.term_id = wp_terms.term_id and wp_term_relationships.object_id in (?) inner join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id';

        (new WithTaxonomy('taxonomies', $b))->setItems([(object)['ID' => 1]])->load();

        $this->assertEquals($sql, $b->toSQL());

        $sql = "select wp_terms.*, wp_term_taxonomy.count, wp_term_taxonomy.taxonomy, wp_term_relationships.object_id from wp_terms inner join wp_term_relationships on wp_terms.term_id = wp_terms.term_id and wp_term_relationships.object_id in (?) inner join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id and wp_term_taxonomy.taxonomy in (?)";
        $b = new Builder($c);
        (new WithTaxonomy('taxonomies', $b))
            ->taxonomy('category')
            ->setItems([(object)['ID' => 1]])
            ->load();

        $this->assertEquals($sql, $b->toSQL());

        m::close();
    }

    /**
     * @test
     */
    function it_can_map_item()
    {
        $taxonomies = [
            (object)[
                'term_id' => 1,
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'taxonomy' => 'category',
                'object_id' => '1',
            ]
        ];

        $c = m::mock(Connection::class);

        $c->shouldReceive('select')->andReturn($taxonomies);

        $b = new Builder($c);


        $data = (new WithTaxonomy('taxonomies', $b))->setItems([(object)['ID' => 1]])->load();

        $this->assertEquals([
            (object)[
                'ID' => 1,
                'taxonomies' => $taxonomies
            ]
        ], $data);
    }
}