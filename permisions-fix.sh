#!/bin/bash

sudo chown -R dock:dock .
sudo chown -R iso27001:iso27001 .g* *.yml *.sh *.cmd
sudo chmod -R 774 .
sudo chmod 754 *.sh
