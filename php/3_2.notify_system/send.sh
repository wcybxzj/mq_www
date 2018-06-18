#!/bin/bash

for i in $(seq 1 10)
do
	php send.php  $i
done
