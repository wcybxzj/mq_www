#!/bin/bash

for i in $(seq 1 10)
do
	php new_task.php  $i
done
