In this project when the first-time user is registered to themself so at that time they have no role and no status.
After registering the first user we have to manually assign a role or status to the user in the database. so that at login time, the admin can go to the admin page and the user can go to the user page.

we have 5 pages.
database file-> in which create database, table query is written and connection to database query also.
index page -> on which the login and registration page button is located.
login page -> for admin and user.
registration page -> for creating new users and updating existing users
admin page -> on admin can edit their profile and assign roles and status to other users;
user page -> It has all the info of the user and an edit button for the user.


Steps:
i am using XAMPP server for my project.

First we have to run the database.php file because we are using the table's in our project if the connection is not established between our app and database then this program will not run.
you can change that file code to established connection and creating database and tables according to your system.
once the database is created with table and after successful connection to database. we can simply run our application by index.php

1. first we do registration. suppose we create user named Govind.
2. then we assign Govind role at admin by using MySQL/maria DB manually because we have to make one person as admin so that admin can manage their user by adminpage.php
3. now we register one more person suppose name as shiv.
4. when shiv login through their credentials then he navigate to userpage.php
5. when Govind login through their credentials then he navigate to adminpage.php
6. now Govind is admin & he can see other users also and assign them any role and status through his adminpage.php
7. That is it now our application is ready to manage multi user role and status. 

