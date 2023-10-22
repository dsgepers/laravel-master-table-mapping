<?php
declare(strict_types=1);

namespace Schepeis\Mapping;

use Atomescrochus\StringSimilarities\Compare;
use Illuminate\Database\Eloquent\Model;

class Mapper
{

    private Compare $handler;
    private string $method;

    public function map(string $type, string $input) : ?Model {
        $result = call_user_func([$this->handler, $this->method], ...[$type, $input]);

        var_dump($type, $input, $result);die;


        return $result;
    }

    public function configure(Compare $class, string $method): self {

        $this->handler = $class;
        $this->method = $method;
        return $this;
    }
}
