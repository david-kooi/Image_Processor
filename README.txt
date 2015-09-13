# imgProc_new
Image Server Codeigniter Repository


Setup
'''''

- Open PhpMyAdmin
- Create a new table
- Import DataBase.sql
- Configure Database name in
	application/config/config.php


New objects can be created, updated, and deleted in the admin page. The admin page can be particular, so it would be useful for someone to provide a walkthrough.

Output Structure
''''''''''''''''

output_folder
	- CompanyA
		- small
		- med
		- large
	- CompanyB
		- small
		- med
		- large
archive
	- CompanyA
		- image1
		- image2
	- CompanyB
		- image11
		- image22

Top level Output folders names

[output_folder & archive] 

can be set in 
application/config/constants.php

Company Specific Output folders are connected to the Companies' name.




Data Structure
''''''''''''''


Company A
	- Option1
		- Ratio1
			- x: 100
			- y: 100
	- Option2
		- Ratio2
			- x: 100
			- y: 200

Company B
	- Option1
		- Ratio1
			- x: 500
			- y: 500
		- Ratio2
			- x: 500
			- y: 1000

Ratios
	- Ratio1	
		- value: 1

	- Ratio2
		- value: 2