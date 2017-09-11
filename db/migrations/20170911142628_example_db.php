<?php


use Phinx\Migration\AbstractMigration;

class ExampleDb extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('transactions', []);

        $table
            ->addColumn('uid', 'string', ['limit' => 23])
            ->addColumn('timestamp', 'datetime', [])
            ->addColumn('transaction_id', 'string', ['limit' => 20])
            ->addColumn('amount', 'decimal', ['precision' => 10, 'scale' => 4])
            ->addColumn('description', 'string', ['limit' => 255])
            ->addColumn('order_reference', 'string', ['limit' => 15])
            ->addColumn('customer_increment_id', 'string', ['limit' => 15])
            ->addColumn('customer_email', 'string', ['limit' => 150])
            ->addColumn('transaction_date', 'datetime', ['default' => '0000-00-00 00:00:00'])
        ;

        $table->addIndex(['uid'], ['unique' => true]);
        $table->addIndex(['transaction_id'], ['unique' => true]);
        $table->addIndex(['order_reference']);
        $table->addIndex(['customer_increment_id']);

        $table->create();
    }
}