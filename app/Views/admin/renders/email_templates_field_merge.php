<div class="card">
    <div class="card-header"> Fields to merge</div>
    <div class="card-body">

        <div class="button-group p-0">
            <input type="button" value="Abstract ID #" onclick="InsertHTML('##ABSTRACT_ID##')" class="btn btn-primary valid" aria-invalid="false">
            <input type="button" value="Assigned ID #" onclick="InsertHTML('##ASSIGNED_ID##')" class="btn btn-primary valid" aria-invalid="false">
            <input type="button" value="Invitation Code #" onclick="InsertHTML('##INVITATION_CODE##')" class="btn btn-primary valid" aria-invalid="false">
            <input type="button" value="Abstract Title" onclick="InsertHTML('##ABSTRACT_TITLE##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Panel/Workshop ID #" onclick="InsertHTML('##PANEL_ID##')" class="btn btn-primary">
            <input type="button" value="Panel/Workshop Title" onclick="InsertHTML('##PANEL_TITLE##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Presenting Author Full Name" onclick="InsertHTML('##PRESENTING_FULL_NAME##')" class="btn btn-primary">
            <input type="button" value="" onclick="InsertHTML('##PRESENTING_LAST_NAME##')" class="btn btn-primary">
            <input type="button" value="Presenting Author Email" onclick="InsertHTML('##PRESENTING_EMAIL##')" class="btn btn-primary">
            <input type="button" value="Presenting Author Prefix" onclick="InsertHTML('##PRESENTING_PREFIX##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Moderator(s)" onclick="InsertHTML('##MODERATORS##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Reviewer Username" onclick="InsertHTML('##REVIEW_USERNAME##')" class="btn btn-primary">
            <input type="button" value="Reviewer Password" onclick="InsertHTML('##REVIEW_PASSWORD##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Session Title" onclick="InsertHTML('##SCHEDULER_SESSION_TITLE##')" class="btn btn-primary">
            <input type="button" value="Session Date" onclick="InsertHTML('##SCHEDULER_SESSION_DATE##')" class="btn btn-primary">
            <input type="button" value="Session Start Time" onclick="InsertHTML('##SCHEDULER_SESSION_START_TIME##')" class="btn btn-primary">
            <input type="button" value="Session End Time" onclick="InsertHTML('##SCHEDULER_SESSION_END_TIME##')" class="btn btn-primary">
            <input type="button" value="Session Room" onclick="InsertHTML('##SCHEDULER_SESSION_ROOM##')" class="btn btn-primary">
            <input type="button" value="Room Capacity" onclick="InsertHTML('##SCHEDULER_ROOM_CAPACITY##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Recipient Full Name" onclick="InsertHTML('##RECIPIENTS_FULL_NAME##')" class="btn btn-primary valid" aria-invalid="false">
            <input type="button" value="Recipient First Name" onclick="InsertHTML('##RECIPIENT_FIRST_NAME##')" class="btn btn-primary">
            <input type="button" value="Recipient Last Name" onclick="InsertHTML('##RECIPIENTS_LAST_NAME##')" class="btn btn-primary">
            <input type="button" value="Recipient Email Address" onclick="InsertHTML('##RECIPIENT_EMAIL_ADDRESS##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Accepted Presentation Preference" onclick="InsertHTML('##ACCEPTED_PRESPREF##')" class="btn btn-primary">
            <input type="button" value="Presentation Date" onclick="InsertHTML('##PRESENTATION_DATE##')" class="btn btn-primary">
            <input type="button" value="Presentation Time" onclick="InsertHTML('##PRESENTATION_TIME##')" class="btn btn-primary">
            <input type="button" value="Admin Comments" onclick="InsertHTML('##ADMIN_COMMENTS##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Today's Date" onclick="InsertHTML('##TODAY_DATE##')" class="btn btn-primary">
            <input type="button" value="Admin Comments to Submitter" onclick="InsertHTML('##ADMIN_COMMENTS_TO_SUBMITTER##')" class="btn btn-primary">
        </div>

        <div class="button-group">
            <input type="button" value="Submitter Name" onclick="InsertHTML('##SUBMITTER_NAME##')" class="btn btn-primary">
            <input type="button" value="Submitter Surname" onclick="InsertHTML('##SUBMITTER_SURNAME##')" class="btn btn-primary">
        </div>

        <hr>

    </div>

</div>

<textarea cols="20" rows="20" id="messageEditor" name="email_message"  class="ckeditor-custom-height"></textarea>