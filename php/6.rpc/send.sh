#!/bin/bash

for i in $(seq 1 10)
do
    php rpc_client.php $i
done

