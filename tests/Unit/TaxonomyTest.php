<?php

namespace Mehedi\WPQueryBuilderTestsExt\Unit;

use Mehedi\WPQueryBuilderExt\Relations\WithTaxonomy;
use Mehedi\WPQueryBuilderTestsExt\BuilderHelper;
use Mehedi\WPQueryBuilderTestsExt\FakeWPDB;
use PHPUnit\Framework\TestCase;

class TaxonomyTest extends TestCase
{
    use BuilderHelper;

    /**
     * @test
     */
    function it_can_add_relation()
    {
        $builder = $this->builder()->addRelation(new WithTaxonomy('taxonomies', $this->builder()));

        $this->assertInstanceOf(WithTaxonomy::class, $builder->with[0]);
    }

    /**
     * @test
     */
    function it_can_generate_currect_sql_query()
    {
        $this->initFakeDB();
        FakeWPDB::add('prepare', function ($sql, ...$args) {
            return sprintf(str_replace('%s', "'%s'", $sql), ...$args);
        });

        FakeWPDB::add('get_results', function ($sql) {
            $this->assertEquals('select wp_terms.*, wp_term_taxonomy.count, wp_term_taxonomy.taxonomy, wp_term_relationships.object_id from wp_terms inner join wp_term_relationships on wp_terms.term_id = wp_terms.term_id and wp_term_relationships.object_id in (1) inner join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id', $sql);
            return [];
        });

        (new WithTaxonomy('taxonomies'))->setItems([(object)['ID' => 1]])->load();

        FakeWPDB::add('get_results', function ($sql) {
            $this->assertEquals("select wp_terms.*, wp_term_taxonomy.count, wp_term_taxonomy.taxonomy, wp_term_relationships.object_id from wp_terms inner join wp_term_relationships on wp_terms.term_id = wp_terms.term_id and wp_term_relationships.object_id in (1) inner join wp_term_taxonomy on wp_term_taxonomy.term_taxonomy_id = wp_term_relationships.term_taxonomy_id and wp_term_taxonomy.taxonomy in ('category')", $sql);
            return [];
        });

        (new WithTaxonomy('taxonomies'))
            ->taxonomy('category')
            ->setItems([(object)['ID' => 1]])
            ->load();
    }

    /**
     * @test
     */
    function it_can_map_item()
    {
        $this->initFakeDB();

        $taxonomies = [
            (object)[
                'term_id' => 1,
                'name' => 'Uncategorized',
                'slug' => 'uncategorized',
                'taxonomy' => 'category',
                'object_id' => '1',
            ]
        ];

        FakeWPDB::add('prepare', function () use (&$taxonomies) {

        });

        FakeWPDB::add('get_results', function () use (&$taxonomies) {
            return $taxonomies;
        });

        $data = (new WithTaxonomy('taxonomies'))->setItems([(object)['ID' => 1]])->load();

        $this->assertEquals([
            (object)[
                'ID' => 1,
                'taxonomies' => $taxonomies
            ]
        ], $data);

        $data = (new WithTaxonomy('taxonomies'))->groupByTaxonomy()->setItems([(object)['ID' => 1]])->load();

        $this->assertEquals([
            (object)[
                'ID' => 1,
                'taxonomies' => [
                    'category' => $taxonomies
                ]
            ]
        ], $data);
    }
}