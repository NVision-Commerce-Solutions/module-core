<?php

declare(strict_types=1);

namespace Commerce365\Core\Block;

use Commerce365\Core\Service\SalesDocumentInterface;
use Magento\Framework\View\Element\Template;

class AbstractList extends Template
{
    protected const VIEW_URL = '';
    protected const URL = '';
    private const SEARCH_STRING_PARAM = 'searchString';

    /**
     * @param $id
     * @return string
     */
    public function getViewUrl($id): string
    {
        return $this->getUrl(static::VIEW_URL, ['id' => $id]);
    }

    protected function processQuery(array $query): array
    {
        $query = $this->addPaginationToQuery($query);
        $query = $this->addSortingToQuery($query);
        $query = $this->addSearchToQuery($query);

        return $query;
    }

    public function getPage()
    {
        return $this->getRequest()->getParam('page') ?? 1;
    }

    public function getPageSize()
    {
        return $this->getRequest()->getParam('pageSize') ?? SalesDocumentInterface::DEFAULT_PAGE_SIZE;
    }

    public function getPreviousPageUrl()
    {
        $urlQuery = ['page' => $this->getPage() - 1];
        $urlQuery = $this->addSearchToQuery($urlQuery);
        $urlQuery = $this->addSortingToQuery($urlQuery);

        return $this->getUrl(static::URL, $urlQuery);
    }

    public function getNextPageUrl()
    {
        $urlQuery = ['page' => $this->getPage() + 1];
        $urlQuery = $this->addSearchToQuery($urlQuery);
        $urlQuery = $this->addSortingToQuery($urlQuery);

        return $this->getUrl(static::URL, $urlQuery);
    }

    /**
     * @param array $query
     * @return array
     */
    protected function addPaginationToQuery(array $query): array
    {
        $query['page'] = $this->getPage();
        $query['pageSize'] = $this->getPageSize();

        return $query;
    }

    public function getSearchString()
    {
        return $this->getRequest()->getParam(self::SEARCH_STRING_PARAM);
    }

    /**
     * @param array $query
     * @return array
     */
    protected function addSearchToQuery(array $query): array
    {
        if ($this->getSearchString()) {
            $query[self::SEARCH_STRING_PARAM] = $this->getSearchString();
        }

        return $query;
    }

    /**
     * @return string
     */
    public function getFilterActionUrl(): string
    {
        return $this->getUrl(static::URL);
    }

    public function getSortColumn()
    {
        return $this->getRequest()->getParam('sortColumn');
    }

    public function getSortDirection()
    {
        return $this->getRequest()->getParam('sortDirection');
    }

    /**
     * @param array $query
     * @return array
     */
    protected function addSortingToQuery(array $query): array
    {
        $query['sortColumn'] = 'Document Date';
        $query['sortDirection'] = 'DESC';
        if ($this->getSortColumn() && $this->getSortDirection()) {
            $query['sortColumn'] = $this->getSortColumn();
            $query['sortDirection'] = $this->getSortDirection();
        }

        return $query;
    }

    public function getSortLink($column)
    {
        $direction = 'ASC';
        if ($this->getSortColumn() === $column && $this->getSortDirection() === 'ASC') {
            $direction = 'DESC';
        }
        $urlQuery = ['sortDirection' => $direction, 'sortColumn' => $column];
        $urlQuery = $this->addSearchToQuery($urlQuery);

        return $this->getUrl(static::URL, $urlQuery);
    }

    public function getSortIcon($column): string
    {
        $sortColumn = $this->getSortColumn() ?? 'Document Date';

        if ($column !== $sortColumn) {
            return '';
        }

        return $this->getSortDirection() === 'ASC' ? '▼' : '▲';
    }
}
