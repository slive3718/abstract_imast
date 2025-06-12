# ABSTRACT ASIA PACIFIC

This inaugural meeting will be for the region, by the region. 
Therefore, a submitted abstract must have a senior author or presenting author 
and a majority of the authors representing the AP/Oceania region.

OWPM2 CodeIgniter application
Tech used
Codeigniter v4.6

## :bangbang: IMPORTANT


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


    
