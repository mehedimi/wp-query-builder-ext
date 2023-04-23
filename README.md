![WP Query Builder Extension Banner](https://banners.beyondco.de/WP%20Query%20Builder%20Extension.png?theme=light&packageManager=composer+require&packageName=mehedimi%2Fwp-query-builder-ext&pattern=architect&style=style_1&description=An+Extension+of+WP+Query+Builder&md=1&showWatermark=0&fontSize=100px&images=document-duplicate)

## WP Query Builder Extension

This is an extension of [WP Query Builder].

### Installation

It is a composer package. You can install it using composer by executing following composer command.

```shell
composer require mehedimi/wp-query-builder-ext
```

It has some relations and plugins of [WP Query Builder].

### Relations

#### WithTaxonomy

With this relation you will be able to load associative taxonomies of specific posts.

```php
// Retrieve all posts with associative taxonomies.
DB::table('posts')
->withRelation(new WithTaxonomy('taxonomies'))
->get()
```

If you need group by taxonomy type then just call `groupByTaxonomy` method on `WithTaxonomy` relation.

```php
// Retrieve all posts with associative taxonomies group by with its type.
DB::table('posts')
->withRelation(new WithTaxonomy('taxonomies'), function(WithTaxonomy $taxonomy){
    $taxonomy->groupByTaxonomy()
})
->get();
```

Optionally you add constrain of taxonomy type by calling `taxonomy` method of `WithTaxonomy` relation.

```php
// This will fetch only category type of taxonomy.
DB::table('posts')
    ->withRelation(new WithTaxonomy('categories'), function(WithTaxonomy $taxonomy){
        $taxonomy->taxonomy('category');
    })->get();
```

### Plugins

#### JoinPostWithMeta

With this plugin, you will be able to join `postmeta` table with `posts` very easily.
You need to just apply that plugin and that's it.
Some examples are given below:

```php
DB::plugin(new JoinPostWithMeta())->select('posts.*')->where('meta_key', 'some meta key name')->get()
```

You could supply the join type on `JoinPostWithMeta` class constructor as well.

```php
// For joining right join `postmeta` with `posts` table
DB::plugin(new JoinPostWithMeta('right'))->select('posts.*')->where('meta_key', 'some meta key name')->get()
```

[WP Query Builder]: https://github.com/mehedimi/wp-query-builder