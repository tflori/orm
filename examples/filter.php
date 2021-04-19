<?php

use ORM\EntityFetcher;
use ORM\EntityFetcher\FilterInterface;
use ORM\EntityManager;

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/entities.php';

/** @var EntityManager $em */

abstract class SearchColumn
{
    /** @var string */
    protected $column;

    public function __construct($column)
    {
        $this->column = $column;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function prepareSearchTerm($searchTerm)
    {
        return '%' . $searchTerm . '%';
    }

    public function getOperator()
    {
        return 'LIKE';
    }
}

class Text extends SearchColumn
{
}

class FilterBySearchTerm implements FilterInterface
{
    /** @var string[]|SearchColumn[] */
    protected $searchColumns;

    /** @var string */
    private $searchTerm;

    /**
     * FilterBySearchTerm constructor.
     * @param string[]|SearchColumn[] $searchColumns
     * @param string $searchTerm
     */
    public function __construct(array $searchColumns, $searchTerm)
    {
        $this->searchColumns = $searchColumns;
        $this->searchTerm = $searchTerm;
    }

    public function apply(EntityFetcher $fetcher)
    {
        $searchTerms = preg_split('/\s+/', $this->searchTerm);
        foreach ($searchTerms as $searchTerm) {
            $parenthesis = $fetcher->parenthesis();
            foreach ($this->searchColumns as $key => $column) {
                if (is_string($column)) {
                    $column = $this->searchColumns[$key] = new Text($column);
                }
                $parenthesis->orWhere(
                    $column->getColumn(),
                    $column->getOperator(),
                    $column->prepareSearchTerm($searchTerm)
                );
            }
            $parenthesis->close();
        }
    }
}

// maybe a stpuid example and you probably want to get the query from the request
$query = 'john doe';
$fetcher = $em->fetch(User::class)->filter(new FilterBySearchTerm(['username', 'password'], $query));
// creates a query similar to this:
// SELECT * FROM table
// WHERE (username LIKE '%john%' OR password LIKE '%john%') AND (username LIKE '%doe%' OR password LIKE '%doe%')
