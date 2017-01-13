@echo off
start sqlite3 kvliquor.db ".backup backup.db"
start "" http://localhost/scheduler/admin.php
exit