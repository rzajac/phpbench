## Benchmarking for PHP

This tool helps writing benchmarks that can be run during testing or some other automated deployment procedure.

## Usage

Command line options:

```
$ phpbench bench -h
Usage:
  bench [options] [--] [<fileOrDir>]

Arguments:
  fileOrDir                      file or directory with benchmarks

Options:
  -f, --format[=FORMAT]          output format: txt, csv. [default: "txt"]
  -i, --iterations[=ITERATIONS]  number of benchmarking iterations [default: 10000]
  -h, --help                     Display this help message
  -q, --quiet                    Do not output any message
  -V, --version                  Display this application version
      --ansi                     Force ANSI output
      --no-ansi                  Disable ANSI output
  -n, --no-interaction           Do not ask any interactive question
  -v|vv|vvv, --verbose           Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
 Run benchmark
```

Run all benchmarks in `examples` directory:

```
$ phpbench bench examples/
```

Benchmark files must end with `Bench.php`.

Run benchmarks from specific file:

```
$ phpbench bench examples/ArrayKeyExistsBench.php
```

## Example output:

Human readable:

```
$ ./phpbench bench examples/

ArrayKeyExistsBench.php
 Benchmark            empty: execution: 100.00 % (0.000416 sec), memory: 100.00 % (536 B), speed: 24 038 462 /sec
 Benchmark            isset: execution: 112.02 % (0.000466 sec), memory: 131.34 % (704 B), speed: 21 459 228 /sec
 Benchmark array_key_exists: execution: 407.93 % (0.001697 sec), memory: 105.97 % (568 B), speed: 5 892 752 /sec

CloneDateBench.php
 Benchmark clone_date: execution: 100.00 % (0.016790 sec), memory: 100.00 % (568 B), speed: 595 593 /sec
 Benchmark   new_date: execution: 240.71 % (0.040416 sec), memory: 101.41 % (576 B), speed: 247 427 /sec

DateToIntBench.php
 Benchmark str_replace_array: execution: 100.00 % (0.007662 sec), memory: 100.00 % (568 B), speed: 1 305 143 /sec
 Benchmark            substr: execution: 145.82 % (0.011173 sec), memory: 105.63 % (600 B), speed: 895 015 /sec
 Benchmark              preg: execution: 156.68 % (0.012005 sec), memory: 100.00 % (568 B), speed: 832 987 /sec
 Benchmark       str_replace: execution: 168.22 % (0.012889 sec), memory: 101.41 % (576 B), speed: 775 856 /sec

DefineConstBench.php
 Benchmark define: execution: 100.00 % (0.005355 sec), memory: 100.00 % (584 B), speed: 1 867 414 /sec
 Benchmark  const: execution: 199.42 % (0.010679 sec), memory: 100.00 % (584 B), speed: 936 418 /sec

MagicCallBench.php
 Benchmark direct: execution: 100.00 % (0.002248 sec), memory: 100.00 % (576 B), speed: 4 448 399 /sec
 Benchmark cached: execution: 497.78 % (0.011190 sec), memory: 108.33 % (624 B), speed: 893 656 /sec
```

CSV format:

```
$ ./phpbench bench examples/ -f csv
Name,Case,Execution Percent,Execution Seconds,Memory Percent,Memory Bytes,Operations Per Second
ArrayKeyExistsBench.php,empty,100.00,0.002145,100.00,536,4662005
ArrayKeyExistsBench.php,isset,106.48,0.002284,128.36,688,4378284
ArrayKeyExistsBench.php,array_key_exists,595.99,0.012784,105.97,568,782228CloneDateBench.php,clone_date,100.00,0.037434,100.00,568,267137
CloneDateBench.php,new_date,241.85,0.090536,101.41,576,110454DateToIntBench.php,str_replace_array,100.00,0.018342,100.00,568,545197
DateToIntBench.php,preg,150.61,0.027624,100.00,568,362005
DateToIntBench.php,str_replace,186.33,0.034176,101.41,576,292603
DateToIntBench.php,substr,332.19,0.060930,105.63,600,164123DefineConstBench.php,define,100.00,0.035186,100.00,584,284204
DefineConstBench.php,const,125.35,0.044107,100.00,584,226722MagicCallBench.php,direct,100.00,0.015866,100.00,576,630279
MagicCallBench.php,cached,500.72,0.079445,108.33,624,125874
```

## Usage in code:

[See code](examples/code.php).

## Installation

Composer install:

```json
{
    "require": {
        "rzajac/phpbench": "~0.6"
    }
}
```

Composer globally:

```
$ composer global require rzajac/phpbench
```
