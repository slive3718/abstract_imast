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
</style>
<main class="light-white">
    <div class="container shadow-lg glass-container">
        <div class="card">
            <div class="row mt-4">
                <div class="text-center ">
                    <label class="alert alert-danger text-center glass-content submissionBtn w-700" role="alert">
                        The submission site is now closed!
                    </label>
                </div>
            </div>
            <hr/>
            <div class="row mt-4">
                <div class="col justify-content-center text-center ">
                    <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Submit Or Update Abstract</button>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-md-12">
                    <div id="landing-page-contents" class="container-fluid p-4">
                        <div class="container ">
                            <div class="text-center mb-3 header-container">
                                <img src="<?= base_url() ?>/public/assets/images/AFS_logo.png" class="header-logo" alt="AFS Logo">

                                <h1 class="mb-4 fw-bolder text-primary header-title"><span class="text-dark">
                                        CASTEXPO & METALCASTING CONGRESS & WFO TECHNICAL FORUM </span>
                                    <br>
                                    <br>Call for Papers & Presentations</h1>
                                <img src="<?= base_url() ?>/public/assets/images/LogoWFO.jpg" class="header-logo" style="height: 220px;" alt="WFO Logo">
                            </div>
                            <p>The American Foundry Society is issuing a call for papers and presentations for the <strong class="text-primary">129th Metalcasting Congress/CastExpo & Technical WFO Forum April 12-15, 2025 in Atlanta, Georgia. </strong></p>

                            <h2 class="mt-5">Technical Papers & Presentations</h2>
                            <p>Covering all issues related to metalcasting, diecasting, and foundry operations, including unique in-plant procedures, new technologies, equipment, products, and other innovations that have contributed to enhanced metalcasting productivity and quality will be considered. Topics of particular interest include:</p>
                            <ul>
                                <li>Melting and molten metal treatment</li>
                                <li>Gating and risering</li>
                                <li>Casting process innovations</li>
                                <li>Additive manufacturing</li>
                                <li>Quality</li>
                                <li>Automation</li>
                                <li>Foundry 4.0</li>
                                <li>Artificial intelligence</li>
                                <li>Finishing</li>
                                <li>Sustainability</li>
                                <li>Energy efficiency</li>
                                <li>Environmental health & safety</li>
                            </ul>

                            <h2 class="mt-5">Management Papers and Presentations</h2>
                            <p>Relating to:</p>
                            <ul>
                                <li>Diversity and Inclusion in Metalcasting</li>
                                <li>Leadership Development for Young Professionals</li>
                                <li>Talent Acquisition and Retention</li>
                                <li>Marketing and Branding in the Metalcasting Industry</li>
                                <li>Workforce Training and Development</li>
                                <li>Employee Wellness and Well-being</li>
                                <li>Performance Management and Feedback</li>
                                <li>Women’s Leadership and Empowerment in Metalcasting</li>
                                <li>Collaboration and Partnership Opportunities</li>
                            </ul>

                            <p>Contact Kim Perna: <a href="mailto:kperna@afsinc.org">kperna@afsinc.org</a></p>

                            <h3 class="mt-5">Submission requirements & deadlines:</h3>
                            <ul>
                                <li><strong>Paper/Presentation Abstract Deadline:</strong> 15 August 2024</li>
                                <li><strong>Paper/Presentation Submission Deadline:</strong> 15 September 2024</li>
                                <li><strong>Panel Presentation Submission Deadline:</strong> 15 October 2024</li>
                                <li><strong>PowerPoint Presentation Deadline:</strong> 15 February 2025</li>
                            </ul>

                            <h3 class="mt-5 fw-bolder">Questions and Technical Support</h3>
                            <p>For technical assistance please click on the <strong class="text-danger">'Support'</strong> icon located on the top right hand of each page for support or contact <a href="mailto:kperna@afsinc.org">kperna@afsinc.org</a>. Email support requests are answered within a 24-hour period.</p>
                            <p>Please note: In order to successfully receive emails regarding your submission, you must add '@owpm2.com' as a safe sender in your email client.
                                <a href="<?=base_url().'public/assets/documents/Safe_Sender_2024.pdf'?>">Click here</a> for help adding safe senders.  Mail client examples include Microsoft Outlook, Apple Mail etc. </p>
                            <p>For questions about the AFS submission guidelines and process for the 129th Metalcasting Congress please contact the AFS Office:</p>
                            <address>
                                American Foundry Society<br>
                                1695 North Penny Lane<br>
                                Schaumburg, IL 60173<br>
                                <a href="mailto:castingcongress@afsinc.org">castingcongress@afsinc.org</a><br>
                                Tel: +1-800/537-4237, +1-847/824-0181 x-246<br>
                                Fax: +1-847/824-7848
                            </address>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col justify-content-center text-center ">
                    <button onClick="window.location.href='<?=base_url()?>login'" class="glass-button w-700  btn btn-primary btn-lg align-center" type="button">Submit Or Update Abstract</button>
                </div>
            </div>

            <div class="row mt-4">
                <div class="text-center ">
                    <label class="alert alert-danger" for=""text-center glass-content submissionBtn w-700" role="alert">
                        The submission site is now closed!
                    </label>
                </div>
            </div>
        </div>
    </div>
</main>