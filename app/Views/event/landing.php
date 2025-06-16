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
                <p class="text-center mb-3 fw-bolder">33rd Scoliosis Research Society (SRS)</p>
                <p class="text-center fw-bolder">International Meeting on Advanced Spine Techniques (IMAST)</p>
                <p class="text-center fw-bolder">Toronto, ON, Canada<br>April 2-5, 2026</p>
                <p class="text-center fw-bolder">Abstract Submission: July 1, 2025 - October 1, 2025</p>

                <div class="row mt-4">
                    <div class="text-center ">
                        <label class="alert alert-success text-center glass-content submissionBtn w-700" role="alert">
                            The submission site is now open!
                        </label>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col justify-content-center text-center ">
                        <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Please click here to submit / edit your submission</button>
                    </div>
                </div>

                <h6 class="fw-bold mt-5">IMPORTANT DATES</h6>
                <ul >
                    <li><strong>Abstract Submission Dates: </strong> July 1 - October 1, 2025, 11:59 PM EDT (US)</li>
                    <li><strong>Abstract Acceptance Notification via email: </strong> December 10, 2025*</li>
                    <li><strong>Abstract Presenter Acceptance Deadline:</strong> January 6, 2026*</li>
                    <li><strong>Meeting Dates:</strong>  <span class="text-danger">April 15-17, 2026</span></li>
                </ul>
                <p>*Dates are subject to change.</p>

                <div class="text-center mt-5">
                    <h6 class="fw-bolder"> <u> Prior to submission of an abstract, be sure to review all information on this page</u></h6>
                </div>

                <h6 class="fw-bold mt-5">SRS MEMBERSHIP</h6>
                <p>Abstracts can be submitted by SRS Members and non-members.</p>

                <h6 class="fw-bold mt-5">MEETING THEME</h6>
                <p>The theme for the 2026 IMAST meeting is xxxxxx</p>

                <h6 class="fw-bold mt-5">ABSTRACT CATEGORIES</h6>
                <p>Abstract submission to IMAST must fit into one of the following categories to be considered for presentation.</p>
                <ul>
                    <li>Adolescent Idiopathic Scoliosis (Non-op, Fusion and VBT)</li>
                    <li>Adult Spinal Deformity</li>
                    <li>AI & Machine Learning*</li>
                    <li>Basic Science / Biomechanics / Genetics</li>
                    <li>Cervical Spine: Deformity & Degenerative</li>
                    <li>Early Onset, Neuromuscular, Congenital Scoliosis & Scheuermann's</li>
                    <li>Emerging and Enabling Technologies (Navigation, Robotics, AR, Optical Nav., Haptic)</li>
                    <li>Infection, Trauma, Tumor</li>
                    <li>Innovations in Education and Training, Simulation</li>
                    <li>Lumbar Diseases (including Spondylolisthesis)</li>
                    <li>Minimally Invasive Approaches (Lateral access surgery, Endoscopic, Outpatient Surgery)</li>
                    <li>Quality / Safety / Value / Complications</li>
                </ul>
                <p><i>*AI & Machine Learning: Abstracts eligible for this category will describe utilization of artificial intelligence, machine learning, predictive analytics, or wearable technology in the diagnosis, treatment planning, intraoperative guidance, or postoperative care of operative spine pathology.</i></p>

                <p>Authors can choose optional sub-categories to further classify their topic. More than one sub-category can be selected:</p>
                <ul>
                    <li>Minimally Invasive</li>
                    <li>Motion Preservation</li>
                    <li>Novel Technique</li>
                    <li>Complications</li>
                </ul>

                <h6 class="fw-bold mt-5">ABSTRACT SUBMISSION INSTRUCTIONS</h6>
                <ul>
                    <li>Abstracts are limited to a maximum of 2,500 characters
                        <ul>
                            <li>Characters in the abstract title, body and table/image caption will be counted (including spaces)</li>
                            <li>Characters in the author and institution listing will not be counted</li>
                        </ul>
                    </li>
                    <li>For each co-author listed, you will need:
                        <ul>
                            <li>Full name</li>
                            <li>Designation/degree</li>
                            <li>Email (please ensure to double check all email addresses entered)</li>
                            <li>Country</li>
                            <li>Institution/affiliation</li>
                        </ul>
                    </li>
                    <li>Limit of one graph, figure or table, max, for each submitted abstract.</li>
                    <li>Submitters may revisit the site as often as necessary to edit the submission (finalized or not) at any time before the submission deadline.</li>
                    <li>No changes can be made to the abstract text after the submission deadline.</li>
                </ul>
                <p>Full directions for abstract submission can be found <a href="#">here</a>.</p>
                <p>Please note: In order to successfully receive emails regarding your submission, you must add '@owpm2.com' as a safe sender in your email client. <a href="<?=base_url('assets/documents/Safe_Senders_2025.pdf')?>">Click here</a> for help adding safe senders.</p>

                <h6 class="fw-bold mt-5">PREVIOUS PUBLICATION</h6>
                <p>Abstracts which have been published online or in print in a journal at the time of submission are NOT eligible for submission.</p>

                <h6 class="fw-bold mt-5">ONE-YEAR FOLLOW-UP REQUIREMENT</h6>
                <p>One-year clinical follow-up is required for abstract submission. The one-year follow up rule does not apply to categories of basic science or biomechanical studies or for topics where one-year follow up is irrelevant such as 30-day re-admission rates.</p>

                <h6 class="fw-bold mt-5">DISCLOSURE AND LICENSING REQUIREMENT</h6>
                <p>ALL authors listed on an abstract will be required to submit a Financial Relationship Disclosure form which must include ALL financial relationships held in the past 24 months with ineligible companies*, regardless of their relevancy to the topic of the abstract(s).</p>
                <p>If authors on an abstract have not completed these required forms prior to the submission deadline, the abstract will NOT be reviewed or considered for the meeting program.</p>

                <div class="mb-4">
                    Visit this link to disclose: <a onClick="window.location.href='<?=base_url().'author'?>'" class="glass-button btn btn-primary align-center" type="button">Disclosure</a>
                </div>

                <p><i>*An ineligible company is an entity whose primary business is producing, marketing, selling, re-selling, or distributing health care goods or services consumed by or on patients. For specific examples of ineligible companies visit accme.org/standards.</i></p>

                <h6 class="fw-bold mt-5">SRS PRODUCT-SPECIFIC LANGUAGE POLICY</h6>
                <p>SRS strongly prefers that pharmaceuticals and proprietary software/databases as well as surgical approaches or specific instrumentation such as "Surgimap, MIMICS, EOS Imaging, ROTEM, XLIF, DLIF, AxiaLIF, Solera, Vertex, Expedium, Mountaineer, Shilla, VEPTR, etc.," are not used in presentations. These terms should be replaced by a generic term or description of the drug, software/database and/or instrumentation or technique unless the use of the term directly impacts learners' understanding of the presentation or data. Instrumentation may also be referred to when the device name is a landmark system that is no longer sold (i.e. Harrington, Cotrel-Dubousset, Luque). It is recognized that studies evaluating a device or devices or comparing different devices or techniques may require the use of product or technique names. If a device trade name or industry developed technique using a trademarked name are used in an abstract or presentation, it will be specifically reviewed by the CME Committee for evaluation of any relevant financial relationships. When there is a known financial relationship, expanded verbal disclosure will be necessary at the time of presentation. Furthermore, if a product name is mentioned, the audience should be informed of why it is necessary to give the name.</p>
                <p>Please review the <a href="#">SRS Product Translation Glossary</a> for more information.</p>

                <h6 class="fw-bold mt-5">ATTENDANCE REQUIREMENTS</h6>
                <ul>
                    <li>By submitting an abstract to IMAST, abstract authors agree that at least one (1) author will attend the meeting and will be available to present on the date and time assigned.</li>
                    <li>In addition, presenting authors are expected to register to attend the meetings for which they are accepted by the deadline set forth in their acceptance notification. Abstracts for which an author is not pre-registered by the dates set forth may be withdrawn from the program.</li>
                    <li>All travel arrangements are the responsibility of the authors.</li>
                </ul>

                <h6 class="fw-bold mt-5">ACCEPTANCE</h6>
                <ul>
                    <li>All selected authors will be required to respond to a formal invitation by the deadline set forth in their acceptance notification.</li>
                    <li>Authors without an account AND an updated disclosure cannot be added after the invitation response deadline. No exceptions will be made.</li>
                </ul>

                <h6 class="fw-bold mt-5">PODIUM PRESENTATION CANCELLATION & NO-SHOW POLICY</h6>
                <p>Authors unable to present their paper in-person should inform SRS as soon as possible with a co-author that can attend to present the paper. If no one is able to attend in-person, please inform the SRS Education Team by the date set forth in your acceptance notification, so an alternate paper can be substituted.</p>
                <p><i>Please note: In the case of a "no-show" podium presentation, the presenting author associated with the "no-show" paper will be prohibited from presenting any papers at the next two IMAST meetings.</i></p>

                <h6 class="fw-bold mt-5">E-POINT PRESENTATION CANCELLATION POLICY</h6>
                <p>At least one author or co-author on all E-Point presentations is expected to register to attend the meeting by the deadline set forth in their acceptance notification.</p>
                <p>If this author cancels their registration prior to the meeting, the E-Point presentation may be withdrawn from the program.</p>

                <h6 class="fw-bold mt-5">CME REVIEW</h6>
                <p>All presentations must clear a CME review conducted by the SRS CME Committee. Presenters may be required to submit slides for review before the meeting and may be asked to make small or significant changes if a bias issue is found.</p>
                <p>Failure to address pertinent CME review issues will result in pulling the presentation from the program and in a one-year ban for abstract presentations and a three-year ban for invited presentations.</p>

                <h6 class="fw-bold mt-5">NOTIFICATIONS</h6>
                <p>All submitters will be notified via email of the status of their submission(s) on December 10, 2025. In the event that you do not receive any notification, please log into the submission site at ANY time to view any recent mail regarding your submissions.</p>

                <h6 class="fw-bold mt-5">TECHNOLOGY TROUBLESHOOTING</h6>
                <ul>
                    <li>Please ensure you are using one of the following browsers: Mozilla Firefox 4+, Safari 5+, Chrome 14+, Microsoft Edge.</li>
                    <li>Browser back and forward arrows have been disabled. Users must use the page progress bar located at the top left of each page.</li>
                    <li>Inactivity of more than 90 minutes on system pages will result in a session time out. Please save your pages intermittently to avoid loss of data.</li>
                    <li>For technical assistance, please email education@srs.org. Support requests are answered within 1 business day.</li>
                    <li>In order to successfully receive emails regarding your submission, you must add '@owpm2.com' as a safe sender in your email client. <a href="<?=base_url('assets/documents/Safe_Senders_2025.pdf')?>">Click here</a> for help adding safe senders.</li>
                </ul>

                <div class="row mt-5">
                    <div class="col justify-content-center text-center ">
                        <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Please click here to submit / edit your submission</button>
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
                    <h6 class="fw-bold mt-5">QUESTIONS?</h6>
                    <p>For further instructions and information on abstract submission, please click <a href="#">here</a>.</p>
                    <p>For any further assistance, please contact <a href="mailto:education@srs.org">education@srs.org</a></p>
                </div>
            </div>
        </div>
    </div>
</main>