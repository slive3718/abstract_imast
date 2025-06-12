<link href="<?=base_url()?>/assets/css/event/landing.css" rel="stylesheet">

<?php echo view('event/common/menu'); ?>
<?php echo view('event/common/event_details'); ?>
<style>
    .header-container {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }
    .header-logo {
        width: 200px;
        height: auto;
        margin: 0 1rem;
    }
    .header-title {
        flex: 1;
        text-align: center;
    }

    .container-landing{
        font-size:16px
    }
</style>
<main class="light-white">
    <div class="container shadow-lg glass-container container-landing">
        <div class="card">
            <div class="container p-5 ">
                <p class="text-center mb-3 fw-bolder">SRS Asia Pacific Meeting</p>
                <p class="text-center fw-bolder">Fukuoka, Japan<br>February 2026</p>
                <p class="text-center fw-bolder">Abstract Submission: May 1, 2025 – June 30, 2025</p>

                <div class="row mt-4">
                    <div class="text-center ">
                        <label class="alert alert-success text-center glass-content submissionBtn w-700" role="alert">
                            The submission site is now open!
                        </label>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col justify-content-center text-center ">
                        <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Please click here to submit / edit  your submission</button>
                    </div>
                </div>

                <h6 class="fw-bold mt-5">IMPORTANT DATES</h6>
                <ul >
                    <li><strong>Abstract Submission: </strong> May 1, 2025 – June 30, 2025, at 11:59 Japan Standard Time</li>
                    <li><strong>Abstract Acceptance Notification via E-mail: </strong> September 4, 2025*</li>
                    <li><strong>Abstract Presenter Acceptance Deadline:</strong>  October 2, 2025*</li>
                    <li><strong>Meeting Dates:</strong>  <span class="text-danger">February 6-7 2026 </span></li>
                </ul>
                <p>*Dates are subject to change.</p>

                <div class="text-center mt-5">
                    <h6 class="fw-bolder"> <u> Prior to submission of an abstract, please review all information on this page</u></h6>
                </div>

                <h6 class="fw-bold mt-5">ASIA & OCEANIA AUTHORSHIP</h6>
                <p>This inaugural meeting will be for the region, by the region. Therefore, a submitted abstract must have a senior author or presenting author and a majority of the authors representing the AP/Oceania region.</p>

                <h6 class="fw-bold mt-5">SRS MEMBERSHIP</h6>
                <p>Abstracts may be submitted by SRS members and non-members.</p>

                <h6 class="fw-bold mt-5">ABSTRACT SUBMISSION SITE</h6>
                <ul>
                    <li>Please log in to submit your abstract here.</li>
                    <li>If you have not submitted a proposal to SRS before or submitted one prior to 2020, you will need to create a new account.</li>
                    <li>Submitters may revisit the site as often as necessary to edit the submission (finalized or not) at any time before the submission deadline.</li>
                    <li>No changes can be made to the abstract text after the submission deadline.</li>
                </ul>

                <h6 class="fw-bold mt-5">ABSTRACT CATEGORIES</h6>
                <ul>
                    <li>Adolescent Idiopathic Scoliosis</li>
                    <li>Adult Spinal Deformity</li>
                    <li>Basic/Translational Science</li>
                    <li>Cervical Deformity</li>
                    <li>Early Onset Scoliosis</li>
                    <li>Kyphosis (SK, Congenital and Pathological Conditions)</li>
                    <li>Miscellaneous Categories (Spondy, Trauma, Tumor, etc.)</li>
                    <li>Neuromuscular/Syndromic Deformity</li>
                    <li>Non-Operative Treatment Methods</li>
                    <li>Quality/Safety/Value/Complications</li>
                </ul>

                <h6 class="fw-bold mt-5">ABSTRACT SUBMISSION: KEY INFORMATION</h6>
                <ul>
                    <li>Abstracts are limited to a maximum of 2,500 characters.</li>
                    <li>Characters in the abstract title, body and table/image caption will be counted (including spaces).</li>
                    <li>Characters in the author and institution listing will not be counted.</li>
                    <li>For each co-author listed, you will need:
                        <ul>
                            <li>Full name</li>
                            <li>Designation/degree</li>
                            <li>Email (please ensure to double check all email addresses are correct)</li>
                            <li>Country</li>
                            <li>Institution/affiliation</li>
                        </ul>
                    </li>
                    <li>Only one (1) attachment is allowed per abstract. Attachments can include graphs, figures, images, or supplemental data.</li>
                    <li>All submitted abstracts will undergo a blinded review. To maintain an unbiased review of all abstracts, please do NOT include any identifying information such as researcher, institution, or study group names in your abstract. Inclusion of any identifying information will disqualify your abstract from review.</li>
                </ul>

                <h6 class="fw-bold mt-5">PREVIOUS PRESENTATION</h6>
                <ul>
                    <li>Abstracts that have been presented or have been accepted to present at an SRS Annual Meeting or IMAST may not be considered for submission to this meeting.</li>
                    <li>Any paper accepted and presented at this meeting can be submitted for SRS Annual Meeting after a two-year follow-up</li>
                    <ul>
                        <li>
                            These papers will be reviewed as an original paper. They will not be considered follow-ups.
                        </li>
                    </ul>
                </ul>

                <h6 class="fw-bold mt-5">ONE-YEAR FOLLOW UP REQUIREMENT</h6>
                <p>One-year clinical follow-up is required for abstract submission. The one-year follow-up rule does not apply to categories of basic science or biomechanical studies or for topics where one-year follow-up is irrelevant, such as 30-day readmission rates.</p>

                <h6 class="fw-bold mt-5">DISCLOSURES</h6>
                <p>
                    The Scoliosis Research Society (SRS) must ensure balance, independence,
                    objectivity and scientific rigor in all educational activities. Therefore,
                    ALL abstract authors must disclose <strong> all financial relationships held in the past 24 months with ineligible companies.* </strong>
                    If the financial relationship has occurred in the past 24 months, but has since ended, we still require you to disclose the relationship.
                </p>

                <div class="mb-4">
                    Visit this link to disclose:  <a onClick="window.location.href='<?=base_url().'author'?>'" class="glass-button  btn btn-primary align-center" type="button">Disclosure</a> <br>
                    <i class="bg-warning bg-opacity-50">Please use the email address that was used in the abstract you are part of. </i>
                </div>

                <p><i>*An ineligible company is an entity whose primary business is producing, marketing, selling, re-selling, or distributing health care goods or services consumed by or on patients. For specific examples of ineligible companies visit accme.org/standards.</i></p>

                <p>
                    <i>
                    If any faculty member in an accepted Education Session does not complete their Relevant Financial
                    Relationship Disclosures, they will be removed from the Session and will need to be replaced.
                    </i>
                </p>
                <h6 class="fw-bold mt-5">NOTIFICATIONS</h6>
                <ul>
                    <li>All submitters will be notified via email of the status of their submission(s). In the event that you do not receive notifications, please log into the submission site at ANY time to view any recent mail regarding your submissions.</li>
                </ul>

                <h6 class="fw-bold mt-5">ACCEPTANCE</h6>
                <ul>
                    <li>All selected authors will be required to respond to a formal invitation by the deadline set forth in their acceptance notification.</li>
                    <li><i class="fw-bolder">Authors without an account AND an updated disclosure cannot be added after the invitation response deadline. <u>No exceptions will be made </u></i>.</li>
                </ul>

                <h6 class="fw-bold mt-5">ATTENDANCE REQUIREMENTS</h6>
                <ul>
                    <li>By submitting an abstract to SRS Asia Pacific Meeting, abstract authors agree that at least one (l) author will attend the meeting and will be available to present, in English, on the date and time assigned.
                        <ul>
                            <li>The Presenting author must be from the regions of Asia or Oceania.</li>
                        </ul>
                    </li>
                    <li>Presenting authors are expected to register to attend the meetings for which they are accepted by
                        the deadline set forth in their acceptance notification. Submissions for which an author is not
                        pre-registered by the dates set forth may be withdrawn from the program.
                    </li>
                    <li>All travel arrangements are the responsibility of the authors.</li>
                </ul>

                <div>

                    <h6 class="fw-bold mt-5">PODIUM PRESENTATION CANCELLATION & NO-SHOW POLICY </h6>
                    <p> Authors unable to present their paper in person should inform SRS as soon as possible which co-author
                        will present in their place. If no one is able to present the paper, SRS must be informed by October 23,
                        2025, so an alternate paper can substituted.
                    </p>
                    <p> <i>In the case of a "no-show" podium presentation, the presenting author associated with the "no-show"
                            paper will be prohibited from presenting any papers at the next two Annual Meetings.</i></p>
                </div>

                <h6 class="fw-bold mt-5">TECHNOLOGY TROUBLESHOOTING</h6>
                <ul>
                    <li>Please use one of the following browsers: Mozilla Firefox 4+, Safari 5+, Chrome 14+, Microsoft Edge</li>
                    <li>Inactivity of more than 90 minutes on system pages will result in a session time out. Please save your pages intermittently to avoid loss of data.</li>
                    <li>To successfully receive emails regarding your submission, you must add "@owpm2.com" as a
                        safe sender in your email client. <a href="<?=base_url('assets/documents/Safe_Senders_2025.pdf')?>">Click here </a> for more information about adding safe senders.</li>
                </ul>

                <div class="row mt-5">
                    <div class="col justify-content-center text-center ">
                        <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Please click here to submit / edit  your submission</button>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="text-center ">
                        <label class="alert alert-success text-center glass-content submissionBtn w-700" role="alert">
                            The submission site is now open!
                        </label>
                    </div>
                </div>

                <div>
                    <h6>QUESTIONS?</h6>
                    <p>For further instructions and details on abstract submission, please click <a href="">here</a>.</p>
                    <p>For questions regarding your submission or technical issues, please contact <a href="education@srs.org">education@srs.org</a> </p>
                </div>
            </div>


        </div>
    </div>
</main>