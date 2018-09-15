---
layout: default
title: Using Bulk Inserts
permalink: /bulkInserts.html
---
## Using Bulk Inserts

Since version 1.5 this library supports bulk inserts. With bulk inserts you reduce the load on your database server and
make batch processes that inserting a lot of data much faster. How they get so fast is described in the appendix of this
document.

The amount of rows inserted in one bulk can be defined with the limit option. The default limit is 20.

There are two options to use bulk inserts: telling the entity manger to use bulk inserts for a specific class or create
a bulk insert object and add entities manually. 

### EntityManager: Use Bulk Inserts

The advantage of this solution is that you don't have to change existing code but on the other hand: if your entities
have a complete primary key they would still cause a select before they get added to bulk insert. If your entities don't
have a primary key yet (for example auto incremented rows) this method is perfectly fine for you:

```php
<?php
$em = ORM\EntityManager::getInstance(Recipient::class);
$em->useBulkInserts(Recipient::class, 50);

// the unchanged code that reads a csv file and creates recipients
while ($row = fgetcsv($fileHandle)) {
    if (empty($row)) {
        continue;
    }
    $recipient = new Recipient([
        'email' => $row[0],
        'name' => $row[1],
    ]);
    $recipient->save();
}

$em->finishBulkInserts(Recipient::class);
```

> Note that you have to finish the bulk inserts also when you are not going to insert more recipients. The reason is
> because there could be new recipients that are not inserted yet (the bulk is not complete).

### Using Bulk Insert Class

The above method is just a simplification for using the `ORM\BulkInsert` class. In the background a `BulkInsert` object
for this entity type gets created and inserts for this type are redirected to the bulk insert.

The `BulkInsert` has several options that also can be used with the other method (`EntityManager::useBulkInserts()` 
returns the `BulkInsert` object):

```php
<?php
$em = ORM\EntityManager::getInstance(Recipient::class);
$bulkInsert = new ORM\BulkInsert($em->getDbal(), Recipient::class, 50);
$bulkInsert->limit(20); // change the limit afterwards
$bulkInsert->onSync(function (array $synced) {
    /** @var ORM\Entity[] $synced */
    // do something with newly created objects
});
$bulkInsert->noUpdates(); // completely disable updating the entities (fire and forget)
$bulkInsert->noAutoIncrement(); // disable autoincrement functionality (restoring entities with primary key)

// reset / revert changes
$bulkInsert->onSync(null);
$bulkInsert->updateEntities();
$bulkInsert->useAutoincrement();

// add elements
$bulkInsert->add(new Recipient, new Reciepient, new Recipient);

// finish the bulk insert
$recipients = $bulkInsert->finish();
```

The option setter return the bulk insert object and can be chained what makes it easier for setup:

```php
<?php
$em = ORM\EntityManager::getInstance(Recipient::class);
$bulkInsert = $em->useBulkInserts(Recipient::class)->noUpdates();
// add the entities
$bulkInsert->add(...$recipients);
$em->finishBulkInserts(Recipient::class);
```

### Appendix: Why it is so much faster?

The term bulk insert is widely known to database engineers: instead of inserting row by row with
 
```sql
INSERT INTO table (cols) VALUES ('values row 1');
INSERT INTO table (cols) VALUES ('values row 2');
-- ...
INSERT INTO table (cols) VALUES ('values row N');
```

you insert several rows comma separated:
 
```sql
INSERT INTO table (cols) VALUES ('values row 1'),('values row 2'),...('values row N');
```
 
While the database still needs time to interpret and insert the values it needs to interpret the statement only once.
But this is just the term bulk insert in the database world and not such a big difference.
 
When we are speaking about object relational mappers there is another thing we need to do when we insert a row to the
database: we have to update the entity. And for auto incremented columns this could also mean that we have to query the
autoincrement value before. In worst case we have to run 3 queries per insert.
 
The bulk inserts are not only optimized inserting data to a table. They also optimize the updating of the entities -
instead of querying each row in a separate select statement all rows are fetched with a single query.
 
#### Problems
 
This library currently supports 3 database management systems: MySQL, Postgres and SQLite. Auto increments will not
work in other DBMS unless you create a database access layer that supports it. Anyway even without auto increments the
queries might be slower than expected.
 
For querying a composite primary key we create a statement like `WHERE (type,number) IN (('branch',1),('leaf',1))`. Not
all DBMS will use an index to filter this query. To solve this problem you can either create a DBAL that handles this
queries better or add an additional non-composite primary key (for example a UUID).
