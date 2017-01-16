@echo off
xcopy "C:\xampp\htdocs\scheduler\db\KVLiquor.db" "C:\xampp\htdocs\scheduler\db\backups\"
ren "C:\xampp\htdocs\scheduler\db\backups\KVLiquor.db" "backup - %date:/=-% %time::=-%.db"
start "" http:\\localhost\scheduler\admin.php
exit