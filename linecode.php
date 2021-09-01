#!/bin/env php
<?php
require_once("common.php");



function whiteSpacesSplit($str, $sepReplacement=" ")
{
    $new_str = preg_replace("/\s+/", $sepReplacement, $str);
    //echo "new str=";var_dump( $new_str);
    return    explode($sepReplacement, $new_str);
}


function extractInfos($error_str)
{

    $_clean=false; //Default is raw HTML

    $sepfile=" in <b>";
    $sepline="</b> on line <b>";
   

    $parts = explode($sepline,$error_str);  
    if(count($parts)==1)
    { 
        //not found, might be text (with no tags)
        $sepline="";
        $sepfile=" in ";
        $sepline=" on line ";   
        $parts = explode($sepline,$error_str);
    }
    if(count($parts)>=2)
    {
        $fileparts = explode($sepfile, trim($parts[0]) );
        echo "FILE parts=";var_dump($fileparts);    
        $numberParts = whiteSpacesSplit(trim($parts[1]));
        echo "number parts="; var_dump( $numberParts);
        if(count($numberParts)>=1)
        {
            $line_number = $numberParts[0];
        }

        if(count($fileparts)>=2)
        {
            $error_label= $fileparts[0]; //non utilisé
            $file_path = $fileparts[1];
        }

        if(!$_clean)
        {   //clean now
            $file_path= strip_tags($file_path);
            $line_number = strip_tags($line_number);
            return array("file"=>$file_path,"line"=>$line_number);
        }
    }

    return false;
}//extractInfos



//configuration file :
//create config dir
$path="./config";
if(!is_dir( $path ) )
{ 
    echoLnColor("Create config folder", ConsoleColors::CYAN);
    return mkdir($path);
}

$configFile="$path/linecode.json";
if( ! file_exists($configFile) )
{
  $defaultContent='
  {  
    "alias":[
      "/var/www/vhosts/MY_WEBSITE1":"c:/user/me/Documents/localpath/MY_WEBSITE1",
      "/var/www/vhosts/MY_WEBSITE2":"c:/user/me/Documents/localpath/MY_WEBSITE2"
         ]
  }
  ';
  file_put_contents($configFile,$defaultContent);  
  echoLnColor("'$configFile' has been created", ConsoleColors::LYELL);
}
exec("kate $configFile");

$configContent=file_get_contents($configFile);
$config = json_decode($configContent);
var_dump( $config );
die("config");
//   data/example.html 
// <br />
// <b>Warning</b>:  Undefined array key 0 in <b>/home/path/test.php</b> on line <b>17</b><br />

//   data/example.txt 
// <br />
// <b>Warning</b>:  Undefined array key 0 in <b>/home/path/test.php</b> on line <b>17</b><br />

$content=file_get_contents("data/example.txt");

$infos = extractInfos(($content));


echo "results = ";var_dump($infos);

exit;

$command=
"export MAIN_DIALOG='
 <vbox>
  <frame Checkbox example>
    <checkbox>
      <label>This is a checkbox...</label>
      <variable>CHECKBOX</variable>
      <action>echo Checkbox is \$CHECKBOX now.</action>
      <action>if true enable:ENTRY</action>
      <action>if false disable:ENTRY</action>
    </checkbox>
    <entry>
      <default>Text in the entry</default>
      <variable>ENTRY</variable>
      <visible>disabled</visible>
    </entry>
    <checkbox>
      <label>I want an OK button NOW!</label>
      <default>true</default>
      <variable>ANOTHER_CHECKBOX</variable>
      <action>if true enable:OKBUTTON</action>
      <action>if false disable:OKBUTTON</action>
    </checkbox>
  </frame>
  <hbox>
   <button ok>
     <variable>OKBUTTON</variable>
   </button>
   <button cancel></button>
  </hbox>
 </vbox>
'

gtkdialog --program=MAIN_DIALOG";

exec($command,$output,$res);

//gtkdialog output
// string(23) "ANOTHER_CHECKBOX="true""
// [10]=>
// string(15) "CHECKBOX="true""
// [11]=>
// string(24) "ENTRY="entry "here" !!!""
// [12]=>
// string(11) "OKBUTTON="""
// [13]=>
// string(9) "EXIT="OK""   OR "EXIT="Cancel""

echo "gtk output:"; var_dump( $output );

?>