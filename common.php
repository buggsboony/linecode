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
  const LBLUE="\033[1;34m"; //Turquoise bold
  const BLUE="\033[0;34m";  //Turquoise thin
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




//startsWith et endsWith
function startsWith($haystack, $needle) { $length = strlen($needle); return (substr($haystack, 0, $length) === $needle); } 
function endsWith($haystack, $needle) { $length = strlen($needle); if ($length == 0) { return true; } return (substr($haystack, -$length) === $needle); }


//2021-09-04 17:19:04 - PHP get home directory
function getHomeDir()
{
   // //Windows
   // ["HOMEDRIVE"]=>
   // string(2) "C:"
   // ["HOMEPATH"]=>
   // string(12) "\Users\john"
   // ["LOCALAPPDATA"]=>
   // string(28) "C:\Users\john\AppData\Local"
   // ["USERPROFILE"]=>  string(14) "C:\Users\john"

   if( isset($_SERVER['HOME']) )
      return $_SERVER['HOME'];
   
   if( isset($_SERVER['USERPROFILE']) )  //Windows
      return $_SERVER['USERPROFILE'];
}


function php_os()
{
   return strtoupper(substr(PHP_OS, 0, 3));
}
//2021-08-14 09:51:15 - Detect server OS with PHP_OS
$OS_WIN=null;
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
   $OS_WIN=true;
} else {
   $OS_WIN=false;
}

//05/09/2021 11:49:57 - PHP windows 10 notification
// Utilisation:   winNotif("This is php notif", "PHP title");
function winNotif($message,$title)
{
   //double quotes for compatibility
   $message=str_replace("'","''",$message);
   $title=str_replace("'","''",$title);

   $winNotif=trim('
   powershell -Command "& {Add-Type -AssemblyName System.Windows.Forms; Add-Type -AssemblyName System.Drawing; $notify = New-Object System.Windows.Forms.NotifyIcon; $notify.Icon = [System.Drawing.SystemIcons]::Information; $notify.Visible = $true; $notify.ShowBalloonTip(0, \''
      .$title.'\', \''
      .$message.'\', [System.Windows.Forms.ToolTipIcon]::None)}"
   ');
   exec($winNotif,$results,$res_code);
   return $res_code;
}

//05/09/2021 12:37:28 - Windows 10 Multiple line textbox function with windows Form
function winMultilineTextBox($label,$title,$default="")
{
    //Issue, fail when label contains quotes like '"'
  $winFormMultilineTextbox=trim('
  powershell -Command "function Read-MultiLineInputBoxDialog([string]$Message, [string]$WindowTitle, [string]$DefaultText){     Add-Type -AssemblyName System.Drawing;     Add-Type -AssemblyName System.Windows.Forms;     <# Create the Label.#>;     $label = New-Object System.Windows.Forms.Label;     $label.Location = New-Object System.Drawing.Size(10,10);     $label.Size = New-Object System.Drawing.Size(280,20);     $label.AutoSize = $true;     $label.Text = $Message;     <# Create the TextBox used to capture the user\'s text.#>;     $textBox = New-Object System.Windows.Forms.TextBox;     $textBox.Location = New-Object System.Drawing.Size(10,40);     $textBox.Size = New-Object System.Drawing.Size(575,200);     $textBox.AcceptsReturn = $true;     $textBox.AcceptsTab = $false;     $textBox.Multiline = $true;     $textBox.ScrollBars = \'Both\';     $textBox.Text = $DefaultText;     <# Create the OK button.#>;     $okButton = New-Object System.Windows.Forms.Button;     $okButton.Location = New-Object System.Drawing.Size(415,250);     $okButton.Size = New-Object System.Drawing.Size(75,25);     $okButton.Text = \"OK\";     $okButton.Add_Click({ $form.Tag = $textBox.Text; $form.Close() });     <# Create the Cancel button.#>;     $cancelButton = New-Object System.Windows.Forms.Button;     $cancelButton.Location = New-Object System.Drawing.Size(510,250);     $cancelButton.Size = New-Object System.Drawing.Size(75,25);     $cancelButton.Text = \"Cancel\";     $cancelButton.Add_Click({ $form.Tag = $null; $form.Close() });     <# Create the form.#>;     $form = New-Object System.Windows.Forms.Form;     $form.Text = $WindowTitle;     $form.Size = New-Object System.Drawing.Size(610,320);     $form.FormBorderStyle = \'FixedSingle\';     $form.StartPosition = \"CenterScreen\";     $form.AutoSizeMode = \'GrowAndShrink\';     $form.Topmost = $True;     $form.AcceptButton = $okButton;     $form.CancelButton = $cancelButton;     $form.ShowInTaskbar = $true;     <# Add all of the controls to the form.#>;     $form.Controls.Add($label);     $form.Controls.Add($textBox);     $form.Controls.Add($okButton);     $form.Controls.Add($cancelButton);     <# Initialize and show the form.#>;     $form.Add_Shown({$form.Activate()});     $form.ShowDialog() > $null  <# Trash the text of the button that was clicked.#>;     <# Return the text that the user entered.#>;     return $form.Tag; };  $multiLineText = Read-MultiLineInputBoxDialog -Message \"'.$label.'\" -WindowTitle \"'.$title.'\" -DefaultText \"'.$default.'\"; echo $multiLineText;"
  ');
  exec($winFormMultilineTextbox,$results,$res_code); 
  if( count( $results)>0 )
  {
    //Rebuild content from table
      return implode("\n",$results);
  }else return "";
}//winMultipleLineTextbox

?>