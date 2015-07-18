#!/bin/bash
lsusb
usb-devices
/etc/udev/rules.d/option.rules
ATTRS{idVendor}=="1c9e", ATTRS{idProduct}=="6061", RUN+="/usr/bin/usbModemScript"
ATTRS{idVendor}=="1c9e", ATTRS{idProduct}=="6061", RUN+="/sbin/modprobe option"
