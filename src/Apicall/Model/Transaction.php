<?php

namespace Ffm\Apicall\Model;

use Illuminate\Database\Eloquent\Model;
use Yadakhov\InsertOnDuplicateKey;

class Transaction extends Model
{
    use InsertOnDuplicateKey;

    protected $table = 'transactions';

    protected $fillable = ['uid', 'timestamp', 'transaction_id', 'transaction_date', 'amount', 'description', 'order_reference', 'customer_increment_id', 'customer_email'];

}