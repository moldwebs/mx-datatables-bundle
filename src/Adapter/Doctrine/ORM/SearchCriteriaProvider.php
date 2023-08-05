<?php

/*
 * Symfony DataTables Bundle
 * (c) Omines Internetbureau B.V. - https://omines.nl/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Omines\DataTablesBundle\Adapter\Doctrine\ORM;

use Doctrine\ORM\Query\Expr\Comparison;
use Doctrine\ORM\QueryBuilder;
use Omines\DataTablesBundle\Column\AbstractColumn;
use Omines\DataTablesBundle\DataTableState;

/**
 * SearchCriteriaProvider.
 *
 * @author Niels Keurentjes <niels.keurentjes@omines.com>
 */
class SearchCriteriaProvider implements QueryBuilderProcessorInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(QueryBuilder $queryBuilder, DataTableState $state)
    {
        $this->processSearchColumns($queryBuilder, $state);
        $this->processGlobalSearch($queryBuilder, $state);
    }

    private function processSearchColumns(QueryBuilder $queryBuilder, DataTableState $state)
    {
        foreach ($state->getSearchColumns() as $searchInfo) {
            /** @var AbstractColumn $column */
            $column = $searchInfo['column'];

            $fields = explode(',', $column->getField());
            $comparisions = [];
            foreach ($fields as $key => $field) {
                $search = $searchInfo['search'];

                if ('' !== trim($search)) {
                    if (null !== ($filter = $column->getFilter())) {
                        if (!$filter->isValidValue($search)) {
                            continue;
                        }
                    }

                    if (substr_count($field,'.') > 1) {
                        $_field = explode('.', $field);
                        $comparisions[] = "MEMBEROFCOLUMN(:search_{$_field[0]}_{$_field[1]}_{$_field[2]}, {$_field[0]}.{$_field[1]}, '{$_field[2]}') = 1";
                        $queryBuilder->setParameter("search_{$_field[0]}_{$_field[1]}_{$_field[2]}", $search);
                    } else {
                        if ($column->getOperator() == 'BETWEEN') $search = $queryBuilder->expr()->literal($search);
                        $search = $column->getRightExpr($search);
                        if ($column->getOperator() != 'BETWEEN' && $column->getOperator() != 'is') $search = $queryBuilder->expr()->literal($search);
                        $comparisions[] = new Comparison($field, $column->getOperator(), $search);
                    }

                }
            }
            if (!empty($comparisions))
                $queryBuilder->andWhere($queryBuilder->expr()->orX(...$comparisions));
        }
//        dd($queryBuilder->getDQL());
    }

    private function processGlobalSearch(QueryBuilder $queryBuilder, DataTableState $state)
    {
        if (!empty($globalSearch = $state->getGlobalSearch())) {
            $expr = $queryBuilder->expr();
            $comparisons = $expr->orX();
            foreach ($state->getDataTable()->getColumns() as $column) {
                if ($column->isGlobalSearchable() && !empty($column->getField()) && $column->isValidForSearch($globalSearch)) {
                    $comparisons->add(new Comparison($column->getLeftExpr(), $column->getOperator(),
                        $expr->literal($column->getRightExpr($globalSearch))));
                }
            }
            $queryBuilder->andWhere($comparisons);
        }
    }
}
