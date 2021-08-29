<?php
 
//2021-07-10 13:49:26 - Class ConsoleColors
class ConsoleColors
{       
   //Usage : echo ConsoleColors::CYAN." $convert_cmd \n".ConsoleColors::DEF;
  const DEF = "\033[0m";//$_DEF ="\e[39m";
  const ORAN="\033[0;33m";
  const RED ="\033[0;31m";
  const GREEN="\033[0;32m"; #echo "$_LRED'$tasks' does not exists.$_DEF\n";
  const LGREEN="\033[1;32m";
  const WHITE="\033[1;37m";
  const LYELL="\033[1;33m";  
  const LRED="\033[1;31m";
  const MAG="\033[0;35m";
  const LMAG="\033[1;35m";
  const CYAN="\033[0;36m";
  const LCYAN="\033[1;36m";
}//ConsoleColors
 
 

#2021-08-08 00:25:09 - Des fonctions pour utiliser la couleur :
 
function echoLn($arg)
{
   echo $arg."\n";
}//echoLn
 
 
function puts($str, $col, $NL=true)
{
   echo($col); 
   echo($str);
   if($NL)echo "\n";
   echo ConsoleColors::DEF;
}
 
function echoColor($str, $col)
{
   return echoLnColor($str, $col, false);
}
 
function echoLnColor($str, $col, $NL=true)
{
   echo($col); 
   echo($str);
   if($NL)echo "\n";
   echo ConsoleColors::DEF;
}
?>