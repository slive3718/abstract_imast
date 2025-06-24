# ABSTRACT ASIA PACIFIC

This inaugural meeting will be for the region, by the region. 
Therefore, a submitted abstract must have a senior author or presenting author 
and a majority of the authors representing the AP/Oceania region.

OWPM2 CodeIgniter application
Tech used
Codeigniter v4.6

## :bangbang: IMPORTANT

    This application is an instance of the Abstract submission system.
    This application is modified to suit the needs of the Asia Pacific region and adapt the centralized abstract submission system using single User database.


#### :question: What is this project?
     This project is an instance of the Abstract submission which is dedicated for a particular client.
     Eg; ASIA-PACIFIC is a client (hence a project) and is accessible through ap.owpm2.com of the web app.

### :Types of users
    1. The submitter - People that submit an abstract/paper.
    2. Author - [presenting-author, co-correspondence and senior-author] -  People that assigned as an author in submissions. 
    3. Reviewers [Regular-reviewer and Deputy reviewer] - People that assigned by admin to review the submission.
    4. Admin - Administrator.

###  1. : Client side:
    Client side have 3 types
    1. Submission -> can be access trough default link ap.owpm2.com
    2. Author -> This is a type of user that the submitter is added during the submission. 
    This user can access the if the submitter already added the user in submission as author using base_url/author 
    3. Acceptance -> This is another login for client that will enable them to accept an abstract. This can be access using base_url/acceptance
    4. Reviewer -> This user is assigned by admin to review a submission. This can be access using base_url/reviewer

### 2. : Admin side :
    1. Admin -> This is the administrator, User that can manage:
    - submitted abstracts,
    - users
    - send email
    - manage emailer
    - manage scheduler
    2. Admin can be access using the base_url/admin


## 3. : Special function :
    1. I added a special function that will empty the database once the submission is done.
    Submission usually takes a year and data of the abstract needs to be clear to open for new submission. Hence this function is created.
    This function SHOULD NOT be access trough the url base_url/truncate/database_name

## 4. : Libraries :
    1. Email are sent tru the PhpMail library. 
        - email attachment
        - email are sent using (new PhpMail())->send($from, $addTo, $subject, $addContent, $attachment = null, $embeded_images = null )
        - embeded-images -> This is a parameter that email use to embed a photo directly to the email body.
    2. File upload can be access directly to library using (new Upload())->doUpload($file, $filePath, $savePath, $fileName)

## 5. : Database :
    1. Database is using MySQL
    2. Database name is abstract_imast
    3. Database table prefix is 
    4. Database table are created using directly.  
    5. Database connection are set by default on ENV file. 
    6. To call default database on model please use "$this->defaultDB", that is set on base model.
    7. To call shared database on model please use "$this->sharedDB", that is set on base model.

## 6. : Controller:
    1. Controller are set to be accessible by default.
    2. Controller are set to be accessible by default using base_url/controller_name
    3. Controller can be accessed using base_url/controller_name/method_name
    4. Controller can be accessed using base_url/controller_name/method_name/parameter
    5. To call default database on controller please use "(new DatabaseModel())->db->database", that is set on base controller.
    6. To call shared databasename on controller please use "$this->shared_db_name", that is set on base controller.
   
## 6. :Connecting a new db_user on database:
    To connect a new db_user to the database, you need to create a new user in the database and grant them the necessary permissions on plesk server.
    to do this you can directly go to https://plesk.owpm.com:8443/phpMyAdmin/index.php then add the username of your default db to shared db to gain access.
    Grant privileges to the user using the following command:
```sql
    GRANT ALL PRIVILEGES ON abstract_shared_db.*
    TO 'abstract_imast'@'localhost'
    IDENTIFIED BY 'dabase_password';
    FLUSH PRIVILEGES;
```
    After that you can access the database using the new user.
    You can also use the same command to grant privileges to other users.

```sql
    GRANT ALL PRIVILEGES ON abstract_imast.*
    TO 'abstract_shrd_db'@'localhost'
    IDENTIFIED BY 'dabase_password';
    FLUSH PRIVILEGES;
```


## 6. : Emailer :
    1. PhpMail is a library that can be used to send email.
    2. PhpMail can be access using class PhpMail.
    3. PhpMail can be used to send email to user, admin, reviewer and author.
    4. PhpMail can be used to send email with attachment and embeded images.

## 
    
