#!/bin/bash
asterisk -rx "database put blacklist $1 1"
