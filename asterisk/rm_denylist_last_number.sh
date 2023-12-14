#!/bin/bash
asterisk -rx "database put denylist $1 0"
