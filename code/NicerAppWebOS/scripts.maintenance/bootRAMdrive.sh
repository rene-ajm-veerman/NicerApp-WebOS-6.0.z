#!/bin/bash
mkdir /ramdisk-storage /ramdisk
mount -t ramfs ramfs /ramdisk-storage
truncate -s 16G /ramdisk-storage/ramdisk.img
mkfs.ext4 /ramdisk-storage/ramdisk.img
mount /ramdisk-storage/ramdisk.img /ramdisk
