#!/bin/bash
sudo groupadd bitnami
sudo useradd -g bitnami bitnami
sudo chown -R bitnami:bitnami .
