# linecode
Just paste error line from browser and linecode will open the file at line error occurred.




//------  Tasks.json for vscode --------------
 {
        "label": "linecode",
        "type": "shell",
        "command": "linecode",
        "args": [
            "--replace", //--swap , -r   
            "/var/remote/website1>C:\\local\\website1;/var/remote/website2>C:\\local\\website2"
        ],
        "group": "build",
        "problemMatcher": []
 }


 //-------- Command line examples ---------------
  linecode --replacement "remote_path>local_path;remote2>local2" -i  #Show Inputbox where to paste error
  linecode --swap "remote_path:local_path" --error "<br/>Error line:xx</b>"
  linecode --error "<br/>erreur line:xx</b>" --editor "code"