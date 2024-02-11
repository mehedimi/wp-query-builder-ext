<?php

namespace Mehedi\WPQueryBuilderExt\Relations;

use Mehedi\WPQueryBuilder\Query\Join;
use Mehedi\WPQueryBuilder\Relations\Relation;

class WithTaxonomy extends Relation
{
    /**
     * Taxonomy types
     *
     * @var array
     */
    protected $taxonomies;

    /**
     * Term relationship table name
     *
     * @var string
     */
    protected $termRelationshipTable = 'term_relationships';

    /**
     * Term taxonomy table name
     *
     * @var string
     */
    protected $termTaxonomyTable = 'term_taxonomy';

    /**
     * Terms table name
     *
     * @var string
     */
    protected $termTableName = 'terms';

    /**
     * Is it need to group by 'taxonomy'
     *
     * @var bool
     */
    protected $groupBy = false;

    /**
     * Set one or many taxonomy type
     *
     * @param array|string $taxonomy
     * @return $this
     */
    public function taxonomy($taxonomy)
    {
        $this->taxonomies = is_array($taxonomy) ? $taxonomy : [$taxonomy];

        return $this;
    }

    /**
     * Set group by taxonomy state
     *
     * @param $state
     * @return $this
     */
    public function groupByTaxonomy($state = true)
    {
        $this->groupBy = $state;
        return $this;
    }

    /**
     * @inheritDoc
     */
    protected function loadedItemsDictionary()
    {
        $items = [];
        $loadedItems = $this->getLoadedItems();

        foreach ($loadedItems as $loadedItem) {
            if ($this->groupBy) {
                $items[$loadedItem->object_id][$loadedItem->taxonomy][] = $loadedItem;
            } else {
                $items[$loadedItem->object_id][] = $loadedItem;
            }
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    protected function getLoadedItems()
    {
        return $this->builder
            ->from($this->termTableName)
            ->select($this->termTableName . '.*', $this->termTaxonomyTable . '.count', $this->termTaxonomyTable . '.taxonomy', $this->termRelationshipTable . '.object_id')
            ->join($this->termRelationshipTable, function (Join $join) {
                $join->on(
                    $this->termTableName . '.term_id', '=', $this->termTableName . '.term_id'
                )->whereIn($this->termRelationshipTable . '.object_id', $this->extractObjectKeys());
            })->join($this->termTaxonomyTable, function (Join $join) {
                $join->on($this->termTaxonomyTable . '.term_taxonomy_id', '=', $this->termRelationshipTable . '.term_taxonomy_id')
                    ->whereColumn($this->termTableName.'.term_id', '=', $this->termTaxonomyTable.'.term_id');

                if (!empty($this->taxonomies)) {
                    if (count($this->taxonomies) === 1) {
                        $join->where($this->termTaxonomyTable . '.taxonomy', $this->taxonomies[0]);
                    } else {
                        $join->whereIn($this->termTaxonomyTable . '.taxonomy', $this->taxonomies);
                    }
                }
            })->get();
    }

    /**
     * Extract object IDs
     *
     * @return array
     */
    public function extractObjectKeys()
    {
        return array_column($this->items, 'ID');
    }

    /**
     * @inheritDoc
     */
    protected function getItemFromDictionary($loadedItems, $item)
    {
        if (array_key_exists($item->ID, $loadedItems)) {
            return $loadedItems[$item->ID];
        }

        return [];
    }
}