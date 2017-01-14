# scheduler
Employee scheduling software for small business. Currently used by my liquor store.  
  
  
Features:
-Assign shifts to employees
-Manage employee information
-Generate payroll reports for employees
-Generate emails to inform employees of upcoming shifts  
  
Technical:
-SQLite Database
-primarialy PHP driven
-Calendar is embedded javascript with embedded PHP...
-.sh or .bat files create backups of database on launch
-If an employee is deleted, they still exist in the database. "employed" attribute is set to "false"
-Shifts will never be lost or deleted

A work in progress (apologies for very messy code -- I was not paid for this).  
Created by Colin Bernard Dec 2016/Jan 2017.
