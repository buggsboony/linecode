#!/bin/env php
<?php
require_once("common.php");
//var_dump( $argv );die("argv!");
$APPNAME="linecode";

function whiteSpacesSplit($str, $sepReplacement=" ")
{
    $new_str = preg_replace("/\s+/", $sepReplacement, $str);
    //echo "new str=";var_dump( $new_str);
    return    explode($sepReplacement, $new_str);
}


function extractInfos($error_str )
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
        //echo "FILE parts=";var_dump($fileparts);    
        $numberParts = whiteSpacesSplit(trim($parts[1]));
        //echo "number parts="; var_dump( $numberParts);
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

//get home directory
$homeDir=getHomeDir();
$open_com="xdg-open";
$OS="LIN";
 //case windows, use powershell
 if(php_os()=="WIN")
 {  
   $OS="WIN";
   $open_com="explorer";
    $local_data=$_SERVER["LOCALAPPDATA"]; // string(28) "C:\Users\john\AppData\Local"
    // echo "local_data=";var_dump( $local_data);
    // die("WINDOWS , please configure config file destination");
    //create config dir
    $path=$local_data.DIRECTORY_SEPARATOR.$APPNAME;
    $configFile=$path.DIRECTORY_SEPARATOR."$APPNAME.json";
 }else
 {
    //create config dir
    $path="$homeDir/.config/$APPNAME";
    $configFile=$path.DIRECTORY_SEPARATOR."$APPNAME.json";
 }//endif php OS
//configuration file :

if(!is_dir( $path ) )
{ 
    echoColor("Create config folder: ", ConsoleColors::CYAN);
    echoLn("'$path'");
    $created = mkdir($path);
}


if( ! file_exists($configFile) )
{
  $defaultContent='
  {  
    "alias":[
      ["/var/www/vhosts/MY_WEBSITE1","c:/user/me/Documents/localpath/MY_WEBSITE1"],
      ["/var/www/vhosts/MY_WEBSITE2","c:/user/me/Documents/localpath/MY_WEBSITE2"]
         ]
  }
  ';
  file_put_contents($configFile,$defaultContent);  
  echoLnColor("'$configFile' has been created", ConsoleColors::ORAN);
}//end create config file

//Read config file
$configContent=file_get_contents($configFile);
$config = json_decode($configContent);
// var_dump( $configFile );
// var_dump( $config );
// die("config");

//   data/example.html 
// <br />
// <b>Warning</b>:  Undefined array key 0 in <b>/home/path/test.php</b> on line <b>17</b><br />

//   data/example.txt 
// <br />
// <b>Warning</b>:  Undefined array key 0 in <b>/home/path/test.php</b> on line <b>17</b><br />

//Temporary filename to store content
$temp_file = sys_get_temp_dir().DIRECTORY_SEPARATOR."lincode_content.txt";

//Reading parameters, récupérer les arguments :
$help='
  -h, --help                      Displays help on commandline options.  
  -v, --version                   Displays version information.
  --author                        Afficher les informations sur l\'auteur.
  --license                       Afficher les informations sur la licence.
                                  pour cette application.
  --replacement, --swap,-r        Replace remote pattern with local pattern separated by a semicolon
  --error,-e, --content           Content error to parse
  -i                              Display graphical inputbox
  --editor                        Specify editor executable command. Default is "code --goto"
  --config,-c                     Opens config directory
  --notif                         Success Notif
  Examples :
  linecode --replacement "remote_path>local_path;remote2>local2" -i  #Show Inputbox where to paste error
  linecode --swap "remote_path:local_path" --error "<br/>Error line:xx</b>"
  linecode --error "<br/>erreur line:xx</b>" --editor "code"
  ';
$editor="code --goto";
$replacement=false;
$gui=false;
$content=null;
$successNotif = false;

for($i=0; $i<count($argv); $i++)
{
    $arg = $argv[$i];
    if( ($arg === "-error" ) || ( $arg === "-e" ) || ( $arg === "--content" ) )
    {
        $content=($argv[$i+1] );
    }   
    if(  ($arg === "--replacement") || ($arg === "--swap") || ($arg === "-r") )
    {
        $replacement=($argv[$i+1] );
    }  

    if(  ($arg === "--editor") )
    {
        $editor=intval($argv[$i+1] );
    } 
    
    if(  ($arg === "--editor") )
    {
       if($OS=="WIN") $successNotif=true;
    } 
    
    if(  ($arg === "--config") || ($arg === "-c")  )
    {
      //open config directory
      exec("$open_com \"$path\"");
      exit;
    } 

    if(  ($arg === "-i")  )
    {
        $gui=true;
    }   
    if(  ($arg === "-h") ||($arg === "--help")  )
    {
        
        echo "Options:\n";
        echoLnColor($help,ConsoleColors::ORAN);
        exit;
    }    
}


//Case there is no content passed, launch GUI
if( !trim($content) )
{
  $gui=true;
}

//Delete temp file:
if(file_exists($temp_file) ) unlink($temp_file);


if($gui)
{
    $label_text="Paste PHP error or warning here";
    //case windows, use powershell
    if(php_os()=="WIN")
    {
        $content=winMultilineTextBox($label_text,$APPNAME);             
    }else
    {
      //http://xpt.sourceforge.net/techdocs/language/gtkdialog/gtkde02-GtkdialogExamples/single/
      //case Linux or other, use gtkdialog
            $gtkdialog='export MAIN_DIALOG=\'
            <vbox>
              <frame '.$label_text.'>       
                <edit accepts-tab="false">          
                  <variable>CONTENT</variable>                  
                  <width>320</width>
                  <height>120</height>                
                </edit>
              </frame>
              <hbox>
              <button ok>  
              <action>echo "$CONTENT">'.$temp_file.' </action>            
              <action type="exit">OK</action> 
              </button>
              <button cancel></button>
              </hbox>
            </vbox>
            \'
 
            gtkdialog --program=MAIN_DIALOG --center';

        
 
        exec($gtkdialog,$results,$iRes);        
        //var_dump( $results); die("pause");

        //parse gtkdialog results
        $exit=false;
        foreach( $results as $result):  
          parse_str($result,$output);        
          if( array_key_exists($key="EXIT",$output) )
          {
             //remove surrounding quotes
             $exit = trim( $output[$key] ,'"');           
          }
        endforeach;
          $exit = strtolower( $exit );
        if( ($exit == "cancel") || ($exit == "abort") )
        {
            echoLnColor("Oups: $exit",ConsoleColors::RED);
            exit;
        }
              
        if(file_exists($temp_file))
        {
            $content = file_get_contents($temp_file);
        }else
        {
            echoLnColor("Oups, no content",ConsoleColors::RED);
            exit;
        }        
        // array(6) {
        //   [0]=>
        //   string(21) "Checkbox is true now."
        //   [1]=>
        //   string(23) "ANOTHER_CHECKBOX="true""
        //   [2]=>
        //   string(15) "CHECKBOX="true""
        //   [3]=>
        //   string(26) "ENTRY="Text in thee entry""
        //   [4]=>
        //   string(11) "OKBUTTON="""
        //   [5]=>
        //   string(9) "EXIT="OK""
        // }  
    }
}//if gui



//TEst avec un fichier fictif
//$content=file_get_contents("data/example_remote.txt"); OK Remplacement ok
//$content=file_get_contents("data/example_local.html"); //Example local ok
//echo "content:\n";var_dump($content);
$infos = extractInfos($content);
//Effectuer le remplacement par alias si nécessaire
if(! property_exists( $config ,"alias") ) $config->alias=array();
//Récupérer par les arguments 
//$config->alias= $argv[];
//Get command line replacement if any and merge to aliases
$aliases = $config->alias;
if($replacement)
{
    $paires=explode(";",$replacement);
    foreach($paires as $pair)
    {
      $exploded = explode(">",$pair);              
      $aliases[] = $exploded;
    }
}//if replacement
//var_dump( $aliases ); die("alioas");

foreach( $aliases as $al):
    $local_file = $infos["file"];
    $remote_path=$al[0];
    $local_path =$al[1];  
    if(startsWith($infos["file"],$remote_path) )
    { //Matches remote website path  
      $local_file = str_replace($remote_path,$local_path,$infos["file"]);       
 
      // var_dump($infos); 
      // echo " Remplacement :";var_dump($local_file);
      // die("remote path=".$remote_path);    
        break;
    }
endforeach;

//echo "results = "; var_dump($infos);
if($infos)
{
  //replace slashes with backslashes:
  if($OS=="WIN")
  {
    $local_file=str_replace("/","\\", $local_file);
  }
  $command = $editor." ".$local_file.":".$infos["line"];
  echoLnColor($command,ConsoleColors::CYAN);
  //Exécuter vscode 
  exec($command,$output,$res_code);
  echo("Result Code: ");  var_dump($res_code);
  if($res_code==0)
  {
    if($successNotif)winNotif("linecode opens '$local_file' ", $APPNAME." success.");
  }
}
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