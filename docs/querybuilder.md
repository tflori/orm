---
layout: default
title: Use QueryBuilder to Interact With Your Database
permalink: /querybuilder.html
---
## Use QueryBuilder to Interact With Your Database

You can run queries, updates, inserts and delete statements without the need to create entities for it by generating
queries using the `QueryBuilder`. To get a `QueryBuilder` you just call the query method on your entity manager
and provide the table you want to build a query for:

```php
/** @var ORM\EntityManager $entityManager */
$query = $entityManager->query('audit');
```

### Building queries

In this chapter we want to learn how to use query builders capabilities to create select clause (columns and modifiers),
join clause, where clause, order clause, group clause etc.

```php
$query = "SELECT $modifiers $columns FROM $table AS $alias $joins " .
         "WHERE $conditions GROUP BY $groupColumns ORDER BY $orderColumns " .
         "LIMIT $limit OFFSET $offset";
```

#### Columns

The columns default to `*`. You can add a single column with `$query->column()` or reset the columns with 
`$query->columns()`. Once you add a column with `$query->column()` the default asterisk gets removed.

A column can also be defined as an expression with arguments and alias.

```php
/** @var ORM\EntityManager $entityManager */
// return all columns but show 'unknown' for the name column if it is null (mysql)
// SELECT *, IFNULL(name, 'unknown') AS name FROM audit
$entityManager->query('audit')
    ->column('*')
    ->column('IFNULL(name, ?)', ['unknown'], 'name');
```

#### Where Conditions

Where conditions are added using the `$query->where()` method. It accepts either a column, operator and value, or an 
expression that may contain question marks as placeholders together with an array of values. You can also omit the
operator what results in the operator `=` or `IN` for arrays.

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
$query->where('col1', 'LIKE', '%term%'); // "col1 LIKE '%term%'"
$query->where('col2', '<', 23); // "col2 < 23"
$query->where('DATE(col3)', '2020-12-31'); // "DATE(col3) = '2020-12-31'"
$query->where('col4 IS NULL'); // "col4 IS NULL"
$query->where('col5', 'IS', null);  // "col5 = 'IS' (CAREFUL! you might not expect this query)
$query->where("IFNULL(col6, ?) <= ?", ['9999-12-31', '2020-08-01']);
```

By default, where conditions are combined with `AND` - to combine them with `OR` use the `$query->orWhere()` method. Use
`$query->parenthesis()` or `$query->orParenthesis()` to open a parenthesis.

Note the logical difference between these two queries:
```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
$query->where('a')->where('b')->orWhere('c'); // "a AND b OR c" (c == true would be enough)
$query->where('a')->parenthesis()->where('b')->orWhere('c')->close(); // "a AND (b OR c)" (a == true is required)
```

"Where in"-conditions can be written with `->where($col, $values)`, `->where($col, 'NOT IN', $values)` or with 
`->whereIn()`, `->whereNotIn()` (and the `->or...` variants). The `->where(Not)In` variants have the advantage that they
also accept an array of columns for combined keys:

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
$query->whereIn(['col1', 'col2'], [['c1v1', 'c2v1'], ['c1v2', 'c2v2']]);
// "(col1, col2) IN (VALUES ('c1v1', 'c2v1'), ('c1v2', 'c2v2'))"
```

#### Joins

Joins can be defined with `$query->join()`, `$query->leftJoin()`, `$query->rightJoin()` and `$query->fullJoin()`. The
methods are all using the same syntax with different join types.

> Note that there is no `INNER JOIN` nor `FULL OUTER JOIN` as this is the default for `JOIN` and `FULL JOIN`. Also the
> `RIGHT JOIN` and `LEFT JOIN` are outer joins.

##### 1. Using a single column for joins with USING clause:

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// LEFT JOIN differences USING(auditId)
$query->leftJoin('differences', 'auditId');
```

##### 2. Using an ON clause without arguments

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// JOIN user ON user.id = audit.userId
$query->join('user', 'user.id = audit.userId');
```

##### 3. Using an ON clause with arguments

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// JOIN article ON article.id = audit.entityId AND article.type = 'news'
$query->join('article', 'article.id = comment.articleId AND article.type = ?', ['news']);
```

##### 4. Using a parenthesis to define the ON clause

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// RIGHT JOIN article ON (article.id = audit.entityId AND audit.entityType = 'article')
$query->rightJoin('article')
    ->where('article.id = audit.entityId')
    ->where('audit.entityType', 'article')
    ->close();
```

##### 5. An empty join (may throw when executed)

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// JOIN some_table
$query->join('some_table', true);
```

> Note that the boolean true is necessary - otherwise you would get a parenthesis

#### Modifiers, Grouping, Sorting, Offset and Limit

To add a modifier you call `$query->modifier(string)`. All modifiers are combined with a space between them.
 
```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// SELECT SQL_NO_CACHE DISTINCT ....
$query->modifier('SQL_NO_CACHE')->modifier('DISTINCT');
```

For grouping call `$query->groupBy(string, array)`. Group by expressions are combined with a comma between them.

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// GROUP BY table.type, table.weight
$query->groupBy('table.type')->groupBy('table.weight');
```

Sorting can be defined with `$query->orderBy()` which accepts an expression with placeholders and the sort direction.

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// ORDER BY FIELD(status, 'todo', 'in_progress', 'done') ASC, last_update DESC
$query->orderBy('FIELD(status, ?, ?, ?)', 'ASC', ['todo', 'in_progress', 'done'])
    ->orderBy('last_update', 'DESC');
```

You can pass limit and offset using `$query->limit(int)` and `$query->offset(int)`. They are self-explanatory but note
that the offset is ignored when no limit is given.

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// LIMIT 10 OFFSET 15
$query->limit(10)->offset(15);
```

#### Limitations of QueryBuilder

There is no having clause (yet) but you could pass that to the last group by:

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// GROUP BY table.type HAVING COUNT(*) > 10
$query->groupBy('table.type HAVING COUNT(*) > 10');
```

Be careful passing question marks `?` in string literals as most functions use question marks to replace arguments. 
Rather use question marks and pass arguments.

```php
/** @var ORM\QueryBuilder\QueryBuilder $query */
// IF(col, ''foo'', ?) -> bad
$query->column('IF(col, \'?\', ?)', ['foo']);
// IF(col, '?', 'foo') -> good
$query->column('IF(col, ?, ?)', ['?', 'foo']);

// danger! don't do that with user input
$foo = 'foo';
$query->column("IF(col, '?', '$foo')");
```

### Select statements

Equal to an entity fetcher you can use the query builder to fetch rows including joins, where conditions, limit and
offset, parenthesis, columns, order and others.

**receiving rows**

```php
$query = $entityManager->query('audit');
$row1 = $query->one(); // first row
$row2 = $query->one(); // second row
$rows = $query->all(); // array of rows starting from 3rd row
$rows = $query->reset()->all(); // array all rows starting from first row
$rows = $query->reset()->one(); // the first row again 
```

**change the fetch mode**

```php
$query = $entityManager->query('audit');
$query->setFetchMode(PDO::FETCH_COLUMN, 0);
$row1Col1 = $query->one();
$query->setFetchMode(PDO::FETCH_ASSOC);
$row2 = $query->one();
```

> Note that this executes the statement - further modifications will not have any effect

### Update statements

You can also use the query builder to execute update statements using the where conditions and joins from the query.

**updating matching rows**

```php
$query = $entityManager->query('user');
$query->where('email', $email)->update(['name' => $name]);
```

### Delete Statements

You can execute delete statements using the defined where conditions on the table.

```php
$query = $entityManager->query('user');
$query->where('email', $email)->delete();
```

### Insert Statements

Insert statements don't use any of the data from the query but still you can execute an insert on the table from
query builder.

```php
$query = $entityManager->query('user');
$query->insert(
    ['email' => 'john.doe@example.com', 'name' => 'john'],
    ['email' => 'jane.doe@example.com', 'name' => 'jane']
);
```
