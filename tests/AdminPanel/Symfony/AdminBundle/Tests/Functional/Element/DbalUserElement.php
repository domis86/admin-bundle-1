<?php

declare (strict_types = 1);

namespace AdminPanel\Symfony\AdminBundle\Tests\Functional\Element;

use AdminPanel\Symfony\AdminBundle\Admin\CRUD\GenericListBatchDeleteElement;
use AdminPanel\Symfony\AdminBundle\Form\Type\BetweenDateType;
use AdminPanel\Symfony\AdminBundle\Tests\Functional\Entity\User;
use AdminPanel\Component\DataGrid\DataGridFactoryInterface;
use AdminPanel\Component\DataSource\DataSourceFactoryInterface;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\HttpKernel\Kernel;

final class DbalUserElement extends GenericListBatchDeleteElement
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        parent::__construct();
        $this->connection = $connection;
    }

    /**
     * Initialize DataGrid.
     *
     * @param \AdminPanel\Component\DataGrid\DataGridFactoryInterface $factory
     * @return \AdminPanel\Component\DataGrid\DataGridInterface
     */
    protected function initDataGrid(DataGridFactoryInterface $factory)
    {
        /* @var $datagrid \AdminPanel\Component\DataGrid\DataGrid */
        $datagrid = $factory->createDataGrid(
            $this->getId() // this is the ID of the element's datagrid
        );
        $datagrid->addColumn('username', 'text', [
            'label' => 'Username',
            'field_mapping' => ['[username]'],
        ]);
        $datagrid->addColumn('hasNewsletter', 'boolean', [
            'label' => 'Has newsletter?',
            'field_mapping' => ['[hasNewsletter]'],
        ]);
        $datagrid->addColumn('createdAt', 'datetime', [
            'label' => 'Created at',
            'field_mapping' => ['[createdAt]'],
            'input_field_format' => 'Y-m-d H:i:s'
        ]);
        $datagrid->addColumn('credits', 'money', [
            'label' => 'Credits',
            'currency' => 'EUR',
            'field_mapping' => ['[credits]'],
        ]);
        $datagrid->addColumn('actions', 'action', [
            'label' => 'Actions',
            'field_mapping' => ['[id]'],
            'actions' => [
                'custom' => [
                    'url_attr' => [
                        'class' => 'btn btn-warning btn-small-horizontal',
                        'title' => 'Custom'
                    ],
                    'route_name' => 'custom_action',
                    'parameters_field_mapping' => [
                        'id' => '[id]'
                    ],
                ]
            ],
        ]);

        return $datagrid;
    }

    /**
     * Initialize DataSource.
     *
     * @param \AdminPanel\Component\DataSource\DataSourceFactoryInterface $factory
     * @return \AdminPanel\Component\DataSource\DataSourceInterface
     */
    protected function initDataSource(DataSourceFactoryInterface $factory)
    {
        /* @var \Doctrine\DBAL\Query\QueryBuilder $queryBuilder */
        $queryBuilder = $this->connection->createQueryBuilder();
        $queryBuilder
            ->select('u.id, u.username, u.hasNewsletter, u.hasSomethingElse, u.credits, u.createdAt')
            ->from('admin_panel_users', 'u')
            ->orderBy('u.createdAt', 'DESC');

        $datasource = $factory->createDataSource('doctrine-dbal', [
            'queryBuilder' => $queryBuilder,
            'countField' => 'u.id',
            'indexField' => 'id'
        ], $this->getId());
        $datasource->setMaxResults(10);

        $datasource->addField('username', 'text', 'like', [
            'field' => 'u.username',
            'form_filter' => true,
            'sortable' => true
        ]);

        $datasource->addField('createdAt', 'datetime', 'between', [
            'field' => 'u.createdAt',
            'form_filter' => true,
            'sortable' => true,
            'form_type'  => version_compare(Kernel::VERSION, '2.8.0', '<') ? 'datetime' : DateTimeType::class,
            'form_from_options' => [
                'widget' => 'single_text',
                'input' => 'string',
            ],
            'form_to_options' => [
                'widget' => 'single_text',
                'input' => 'string',
            ]
        ]);
        $datasource->addField('createdAtDate', 'datetime', 'between', [
            'field' => 'u.createdAt',
            'form_filter' => true,
            'form_type'  => version_compare(Kernel::VERSION, '2.8.0', '<') ? 'between_dates' : BetweenDateType::class,
            'form_from_options' => [
                'date_format' => 'Y-m-d 00:00:00',
                'widget' => 'single_text',
                'input' => 'string',
            ],
            'form_to_options' => [
                'date_format' => 'Y-m-d 23:59:59',
                'widget' => 'single_text',
                'input' => 'string',
            ]
        ]);

        $datasource->addField('credits', 'number', 'eq', [
            'field' => 'u.credits',
            'form_filter' => true,
            'sortable' => true
        ]);

        $datasource->addField('hasNewsletter', 'boolean', 'isNull', [
            'field' => 'u.has_newsletter',
            'form_filter' => true
        ]);

        $datasource->addField('hasSomethingElse', 'checkbox', 'eq', [
            'field' => 'u.hasSomethingElse',
            'form_options' => [
                'label' => 'Something'
            ],
            'form_filter' => true
        ]);

        return $datasource;
    }

    /**
     * ID will appear in routes:
     * - http://example.com/admin/list/{name}
     * - http://example.com/admin/form/{name}
     * etc.
     *
     * @return string
     */
    public function getId()
    {
        return 'admin_users_dbal';
    }

    /**
     * @param mixed $index
     */
    public function delete($index)
    {
        $this->connection->delete('admin_panel_users', ['id' => $index]);
    }
}