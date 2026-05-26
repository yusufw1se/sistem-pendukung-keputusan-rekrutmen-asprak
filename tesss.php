<?php
echo "tes python";
$python = 'C:\Users\yusufw1se\AppData\Local\Python\bin\python.exe';

echo shell_exec("\"$python\" --version");