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
