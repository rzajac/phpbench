## Benchmarking for PHP

This tool will help you write benchmarks that can be run during testing or some other automated deployment procedures.

## Usage

Command line options:

```
$ ./phpbench
PHPBench usage:
	-o, --output[=<string:txt>]
		output format: txt, csv

	-d, --dir[=<string>]
		directory

	-f, --file[=<string>]
		file
```

Run all benchmarks in examples directory:

```
$ phpbench -d ./examples
```

Run benchmarks from specific file:

```
$ phpbench -f ./examples/ArrayKeyExistsBench.php
```

## Example output:

```
$ ./phpbench -d examples

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

## Usage in code:

[See code](examples/code.php).

## Installation

Composer install:

```json
{
    "require": {
        "rzajac/phpbench": "~0.5"
    }
}
```

Composer globally:

```
$ ./composer.phar global require rzajac/phpbench
```
