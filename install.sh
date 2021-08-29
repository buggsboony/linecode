#!/bin/bash

#install stuff
what=${PWD##*/}   
extension=.php
#peut être extension vide

echo "installation de gtkdialog"
sudo pamac install gtkdialog

echo "Set executable..."
chmod +x $what$extension
#echo "lien symbolique vers usr bin"
sudo ln -s "$PWD/$what$extension" /usr/bin/$what
echo "done."

