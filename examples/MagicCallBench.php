<?php

/**
 * Class A.
 *
 * @method string doSomethingC(string $code, string $language)
 */
class AClass
{
    public function __call($name, $args)
    {
        if (substr($name, -1) !== 'C') {
            return '';
        }

        $method = substr($name, 0, -1);

        switch (count($args)) {
            case 1:
                return $this->$method($args[0]);
            break;

            case 2:
                return $this->$method($args[0], $args[1]);
            break;

            default:
                return call_user_func_array([$this, $method], $args);
        }
    }

    public function doSomething($code, $language)
    {
        return $code.$language;
    }
}

$a = new AClass();

$benchmarks = [];

$benchmarks['direct'] = function ($iterations) use ($a) {
    for ($i = 0; $i < $iterations; ++$i) {
        $value = $a->doSomething('Poland', 'Polish');

        if ($value != 'PolandPolish') {
            throw new Exception('WTF!');
        }
    }
};

$benchmarks['cached'] = function ($iterations) use ($a) {
    for ($i = 0; $i < $iterations; ++$i) {
        $value = $a->doSomethingC('Poland', 'Polish');

        if ($value != 'PolandPolish') {
            throw new Exception('WTF!');
        }
    }
};

return $benchmarks;
